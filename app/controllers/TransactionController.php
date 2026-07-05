<?php
// ============================================================
//  app/controllers/TransactionController.php — Fichier commenté
// ============================================================

// Classe TransactionController : implémente la logique métier pour cette partie de l’application
class TransactionController extends Controller {

// Méthode index : gère index. 
    public function index(): void {
        Auth::requireAuth();
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/Service.php';

        $filtres = [
            'id_service'  => $this->get('service'),
            'id_type'     => $this->get('type'),
            'statut'      => $this->get('statut'),
            'search'      => trim((string)$this->get('search')),
            'date_debut'  => $this->get('date_debut'),
            'date_fin'    => $this->get('date_fin'),
        ];
        $agencyId = AgencyContext::resolveAgencyId();
        if ($agencyId !== null) {
            $filtres['id_agence'] = $agencyId;
        }

        $txModel      = new Transaction();
        $serviceModel = new Service();
        require_once APP_PATH . '/models/TypeOperation.php';
        $typeModel = new TypeOperation();

        $this->render('transactions/index', [
            'transactions' => $txModel->getAllWithDetails($filtres),
            'services'     => $serviceModel->getAllActifs(),
            'types'        => $typeModel->all('libelle'),
            'filtres'      => $filtres,
            'statuts'      => ['VALIDEE' => 'Validée', 'EN_COURS' => 'En cours', 'ANNULEE' => 'Annulée'],
        ], 'Transactions');
    }

// Méthode create : gère create. 
    public function create(): void {
        Auth::requireRole(['AGENT', 'SUPERVISEUR', 'DG']);
        require_once APP_PATH . '/models/Service.php';
        require_once APP_PATH . '/models/TypeOperation.php';
        require_once APP_PATH . '/models/Agence.php';

        $serviceModel = new Service();
        $typeModel    = new TypeOperation();
        $agenceModel  = new Agence();
        $services     = $serviceModel->getAllActifs();
        $typesByService = [];

        foreach ($services as $service) {
            $typesByService[(int)$service['id_service']] = $typeModel->getByService((int)$service['id_service']);
        }

        $this->render('transactions/create', [
            'services'        => $services,
            'typesOperations' => $typeModel->all(),
            'typesByService'  => $typesByService,
            'agences'         => $agenceModel->getAllActives(),
        ], 'Nouvelle transaction');
    }

// Méthode store : gère store. 
    public function store(): void {
        Auth::requireRole(['AGENT', 'SUPERVISEUR', 'DG']);
        $this->verifyCsrf();

        $idService = $this->post('id_service');
        $idType    = $this->post('id_type');
        $montant   = $this->post('montant');
        $reference = $this->post('reference', '');
        $note      = $this->post('note', '');
        $idAgence  = $this->post('id_agence');
        $motifTransaction = $this->post('motif_transaction', '');
        $nomExpediteur = $this->post('nom_expediteur', '');
        $expediteurIdentifiant = $this->post('expediteur_identifiant', '');
        $expediteurTelephone = $this->post('expediteur_telephone', '');
        $nomBeneficiaire = $this->post('nom_beneficiaire', '');
        $beneficiaireIdentifiant = $this->post('beneficiaire_identifiant', '');
        $beneficiaireTelephone = $this->post('beneficiaire_telephone', '');

        $errors = $this->validate([
            'id_service' => $idService,
            'id_type'    => $idType,
            'montant'    => $montant,
            'reference'  => $reference,
            'note'       => $note,
            'motif_transaction' => $motifTransaction,
            'nom_expediteur' => $nomExpediteur,
            'expediteur_identifiant' => $expediteurIdentifiant,
            'expediteur_telephone' => $expediteurTelephone,
            'nom_beneficiaire' => $nomBeneficiaire,
            'beneficiaire_identifiant' => $beneficiaireIdentifiant,
            'beneficiaire_telephone' => $beneficiaireTelephone,
        ], [
            'id_service' => 'required|integer|positive',
            'id_type'    => 'required|integer|positive',
            'montant'    => 'required|numeric|positive',
            'reference'  => 'max_length:255',
            'note'       => 'max_length:1000',
            'motif_transaction' => 'max_length:255',
            'nom_expediteur' => 'max_length:255',
            'expediteur_identifiant' => 'max_length:100',
            'expediteur_telephone' => 'max_length:50',
            'nom_beneficiaire' => 'max_length:255',
            'beneficiaire_identifiant' => 'max_length:100',
            'beneficiaire_telephone' => 'max_length:50',
        ]);

        if ($idService <= 0 || $idType <= 0 || (is_numeric($montant) && (float)$montant <= 0)) {
            $errors[] = 'Le montant doit être supérieur à zéro et le service/type doivent être sélectionnés.';
        }

        if (!empty($errors)) {
            Session::flash('error', implode(' ', $errors));
            $this->redirect('transactions/create');
        }

        $idService = (int) $idService;
        $idType    = (int) $idType;
        $montant   = (float) str_replace(',', '.', $montant);
        $agencyId = AgencyContext::resolveAgencyId();
        $idAgence  = $idAgence !== '' && $idAgence !== null ? (int)$idAgence : $agencyId;

        // Chargement des modèles
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/Service.php';
        require_once APP_PATH . '/models/Agence.php';
        require_once APP_PATH . '/models/TypeOperation.php';
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/MouvementSolde.php';
        require_once APP_PATH . '/models/SeuilAlerte.php';
        require_once APP_PATH . '/models/AlerteSolde.php';
        require_once APP_PATH . '/models/CommissionConfig.php';
        require_once APP_PATH . '/models/CommissionTransaction.php';

        $txModel      = new Transaction();
        $serviceModel = new Service();
        $agenceModel  = new Agence();
        $typeModel    = new TypeOperation();
        $soldeModel   = new SoldeService();
        $mvtModel     = new MouvementSolde();
        $seuilModel   = new SeuilAlerte();
        $alerteModel  = new AlerteSolde();
        $configModel  = new CommissionConfig();
        $commModel    = new CommissionTransaction();

        try {
            $txModel->beginTransaction();

            $service = $serviceModel->find($idService);
            $agence = $idAgence ? $agenceModel->find($idAgence) : null;
            $typeOp = $typeModel->find($idType);

            if (!$service || !$typeOp) {
                throw new RuntimeException('Service ou type d\'opération introuvable.');
            }

            $isInternational = $this->isInternationalService($service);
            if ($isInternational) {
                if (!$idAgence || !$agence) $errors[] = 'Veuillez choisir l\'agence qui effectue la transaction.';
                if ($motifTransaction === '') $errors[] = 'Veuillez renseigner le motif de la transaction.';
                if ($nomExpediteur === '' || $expediteurIdentifiant === '' || $expediteurTelephone === '') {
                    $errors[] = 'Veuillez renseigner les informations complètes de l\'expéditeur.';
                }
                if ($nomBeneficiaire === '' || $beneficiaireIdentifiant === '' || $beneficiaireTelephone === '') {
                    $errors[] = 'Veuillez renseigner les informations complètes du bénéficiaire.';
                }
            }

            if (!empty($errors)) {
                throw new InvalidArgumentException(implode(' ', $errors));
            }

            $typeMouvement = $this->deduireTypeMouvement($typeOp);

            // 1. Insérer la transaction
            $idTransaction = $txModel->create([
                'id_service' => $idService,
                'id_type'    => $idType,
                'id_user'    => Auth::id(),
                'agence'     => $agence['nom'] ?? null,
                'id_agence'  => $idAgence,
                'reference'  => $reference,
                'nom_expediteur' => $nomExpediteur ?: null,
                'expediteur_identifiant' => $expediteurIdentifiant ?: null,
                'expediteur_telephone' => $expediteurTelephone ?: null,
                'nom_benefis' => $nomBeneficiaire ?: null,
                'beneficiaire_identifiant' => $beneficiaireIdentifiant ?: null,
                'beneficiaire_telephone' => $beneficiaireTelephone ?: null,
                'code_operation' => $reference ?: null,
                'nature_operation' => $typeOp['libelle'],
                'produit' => $service['nom'],
                'type_de_operation' => $typeOp['libelle'],
                'montant'    => $montant,
                'motif_transaction' => $motifTransaction ?: null,
                'nature_transaction' => 'FINANCIER',
                'type_mouvement' => $typeMouvement,
                'affecte_stock' => !empty($typeOp['impact_float']) ? 1 : 0,
                'affecte_caisse' => !empty($typeOp['impact_caisse']) ? 1 : 0,
                'notes' => $note ?: null,
                'note'       => $note,
                'statut'     => 'VALIDEE',
            ]);

            // 3. Mettre à jour Float si impacté
            if ($typeOp['impact_float'] !== 0) {
                $soldeFloat = $soldeModel->getSolde($idService, 'FLOAT', $agencyId);
                if ($soldeFloat) {
                    $nature    = $typeOp['impact_float'] > 0 ? 'CREDIT' : 'DEBIT';
                    $resultats = $soldeModel->mettreAJour($soldeFloat['id_solde'], $montant, $nature);
                    $mvtModel->createMouvement(
                        $idTransaction, $soldeFloat['id_solde'],
                        $nature, $montant,
                        $resultats['solde_avant'], $resultats['solde_apres'],
                        $typeOp['libelle']
                    );
                    // Vérifier seuil Float
                    if ($seuilModel->estAtteint($soldeFloat['id_solde'], $resultats['solde_apres'])) {
                        $seuilFloat = $seuilModel->getBySolde($soldeFloat['id_solde']);
                        $alerteModel->create([
                            'id_seuil'          => $seuilFloat['id_seuil'],
                            'message'           => "FLOAT {$typeOp['libelle']} insuffisant",
                            'montant_au_moment' => $resultats['solde_apres'],
                        ]);
                    }
                }
            }

            // 4. Mettre à jour Caisse si impactée
            if ($typeOp['impact_caisse'] !== 0) {
                $soldeCaisse = $soldeModel->getSolde($idService, 'CAISSE', $agencyId);
                if ($soldeCaisse) {
                    $nature    = $typeOp['impact_caisse'] > 0 ? 'CREDIT' : 'DEBIT';
                    $resultats = $soldeModel->mettreAJour($soldeCaisse['id_solde'], $montant, $nature);
                    $mvtModel->createMouvement(
                        $idTransaction, $soldeCaisse['id_solde'],
                        $nature, $montant,
                        $resultats['solde_avant'], $resultats['solde_apres'],
                        $typeOp['libelle']
                    );
                    // Vérifier seuil Caisse
                    if ($seuilModel->estAtteint($soldeCaisse['id_solde'], $resultats['solde_apres'])) {
                        $seuilCaisse = $seuilModel->getBySolde($soldeCaisse['id_solde']);
                        $alerteModel->create([
                            'id_seuil'          => $seuilCaisse['id_seuil'],
                            'message'           => "CAISSE {$typeOp['libelle']} insuffisante",
                            'montant_au_moment' => $resultats['solde_apres'],
                        ]);
                    }
                }
            }

            // 5. Calculer et enregistrer la commission
            $config = $configModel->getConfig($idService, $idType);
            if ($config) {
                $montantComm = $configModel->calculer($config, $montant);
                if ($montantComm > 0) {
                    $commModel->create([
                        'id_transaction'     => $idTransaction,
                        'id_config'          => $config['id_config'],
                        'source'             => $config['source'],
                        'montant_commission' => $montantComm,
                        'est_benefice'       => 1,
                    ]);
                }
            }

            $txModel->commit();
            Session::flash('success', 'Transaction enregistrée avec succès !');
            $this->redirect('transactions/' . $idTransaction);

        } catch (Exception $e) {
            $txModel->rollback();
            $message = $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'Erreur lors de l\'enregistrement. Veuillez réessayer plus tard.';
            Session::flash('error', $message);
            $this->redirect('transactions/create');
        }
    }

    private function isInternationalService(array $service): bool {
        $name = strtolower((string)($service['nom'] ?? ''));
        $category = strtoupper((string)($service['categorie'] ?? ''));

        return $category === 'INTERNATIONAL'
            || str_contains($name, 'ria')
            || str_contains($name, 'western')
            || str_contains($name, 'moneygram')
            || str_contains($name, 'cash');
    }

    private function deduireTypeMouvement(array $typeOp): string {
        $impactCaisse = (int)($typeOp['impact_caisse'] ?? 0);
        $impactFloat = (int)($typeOp['impact_float'] ?? 0);
        $impactPrincipal = $impactCaisse !== 0 ? $impactCaisse : $impactFloat;

        if ($impactPrincipal > 0) return 'ENTREE';
        if ($impactPrincipal < 0) return 'SORTIE';
        return 'NEUTRE';
    }

// Méthode show : gère show. 
    public function show(string $id): void {
        Auth::requireAuth();
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/MouvementSolde.php';
        require_once APP_PATH . '/models/CommissionTransaction.php';

        $txModel   = new Transaction();
        $mvtModel  = new MouvementSolde();
        $commModel = new CommissionTransaction();

        $transaction = $txModel->getWithDetails((int)$id);
        if (!$transaction) {
            $this->redirect('transactions?error=not_found');
        }

        $this->render('transactions/show', [
            'transaction' => $transaction,
            'mouvements'  => $mvtModel->getByTransaction((int)$id),
        ], 'Détail transaction #' . $id);
    }

// Méthode edit : gère edit. 
    public function edit(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'DG']);
        require_once APP_PATH . '/models/Transaction.php';

        $txModel = new Transaction();
        $transaction = $txModel->getWithDetails((int)$id);

        if (!$transaction) {
            $this->redirect('transactions?error=not_found');
        }

        if ($transaction['statut'] === 'ANNULEE') {
            Session::flash('error', 'Cette transaction est annulée et ne peut pas être modifiée.');
            $this->redirect('transactions/' . $id);
        }

        $this->render('transactions/edit', [
            'transaction' => $transaction,
        ], 'Modifier transaction #' . $id);
    }

// Méthode update : gère update. 
    public function update(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'DG']);
        $this->verifyCsrf();

        require_once APP_PATH . '/models/Transaction.php';

        $reference = trim($this->post('reference', ''));
        $note      = trim($this->post('note', ''));

        if (mb_strlen($reference, 'UTF-8') > 255 || mb_strlen($note, 'UTF-8') > 1000) {
            Session::flash('error', 'Les champs sont trop longs. Référence max 255 caractères, note max 1000 caractères.');
            $this->redirect('transactions/' . $id . '/edit');
        }

        $txModel = new Transaction();
        $transaction = $txModel->find((int)$id);
        if (!$transaction) {
            $this->redirect('transactions?error=not_found');
        }

        if ($transaction['statut'] === 'ANNULEE') {
            Session::flash('error', 'Cette transaction est annulée et ne peut pas être modifiée.');
            $this->redirect('transactions/' . $id);
        }

        $txModel->update((int)$id, [
            'reference' => $reference,
            'note'      => $note,
        ]);

        Session::flash('success', 'Transaction mise à jour avec succès.');
        $this->redirect('transactions/' . $id);
    }

// Méthode cancel : gère cancel. 
    public function cancel(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'DG']);
        $this->verifyCsrf();
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/MouvementSolde.php';
        require_once APP_PATH . '/models/CommissionTransaction.php';

        $txModel = new Transaction();
        $soldeModel = new SoldeService();
        $mvtModel = new MouvementSolde();
        $commModel = new CommissionTransaction();
        $idTransaction = (int)$id;

        try {
            $txModel->beginTransaction();

            $transaction = $txModel->find($idTransaction);
            if (!$transaction) {
                throw new InvalidArgumentException('Transaction introuvable.');
            }

            if (($transaction['statut'] ?? '') === 'ANNULEE') {
                throw new InvalidArgumentException('Cette transaction est déjà annulée.');
            }

            if (($transaction['statut'] ?? '') !== 'VALIDEE') {
                throw new InvalidArgumentException('Seules les transactions validées peuvent être annulées.');
            }

            $mouvements = $mvtModel->getByTransaction($idTransaction);
            foreach ($mouvements as $mouvement) {
                $natureInverse = $mouvement['nature'] === 'CREDIT' ? 'DEBIT' : 'CREDIT';
                $montant = (float)$mouvement['montant'];
                $resultats = $soldeModel->mettreAJour((int)$mouvement['id_solde'], $montant, $natureInverse);

                $mvtModel->createMouvement(
                    $idTransaction,
                    (int)$mouvement['id_solde'],
                    $natureInverse,
                    $montant,
                    $resultats['solde_avant'],
                    $resultats['solde_apres'],
                    'Annulation comptable du mouvement #' . $mouvement['id_mouvement']
                );
            }

            foreach ($commModel->getByTransaction($idTransaction) as $commission) {
                if ((float)$commission['montant_commission'] == 0.0) {
                    continue;
                }

                $commModel->create([
                    'id_transaction' => $idTransaction,
                    'id_config' => (int)$commission['id_config'],
                    'source' => $commission['source'],
                    'montant_commission' => -abs((float)$commission['montant_commission']),
                    'est_benefice' => (int)$commission['est_benefice'],
                ]);
            }

            $txModel->update($idTransaction, [
                'statut' => 'ANNULEE',
                'note' => trim((string)($transaction['note'] ?? '') . "\nAnnulation comptable effectuee le " . date('Y-m-d H:i:s') . ' par utilisateur #' . Auth::id()),
            ]);

            $txModel->commit();
            Session::flash('success', 'Transaction annulée avec écritures comptables inverses.');
        } catch (InvalidArgumentException $e) {
            $txModel->rollback();
            Session::flash('error', $e->getMessage());
        } catch (Exception $e) {
            $txModel->rollback();
            Session::flash('error', 'Annulation impossible. Aucune écriture comptable n’a été modifiée.');
        }

        $this->redirect('transactions/' . $idTransaction);
    }
}
