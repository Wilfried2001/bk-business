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

        $serviceModel = new Service();
        $typeModel    = new TypeOperation();

        $this->render('transactions/create', [
            'services'        => $serviceModel->getAllActifs(),
            'typesOperations' => $typeModel->all(),
        ], 'Nouvelle transaction');
    }

// Méthode store : gère store. 
    public function store(): void {
        Auth::requireRole(['AGENT', 'SUPERVISEUR', 'DG']);
        $this->verifyCsrf();

        $idService = (int) $this->post('id_service');
        $idType    = (int) $this->post('id_type');
        $montant   = (float) $this->post('montant');
        $reference = $this->post('reference', '');
        $note      = $this->post('note', '');

        // Validation
        if (!$idService || !$idType || $montant <= 0) {
            Session::flash('error', 'Données invalides. Veuillez vérifier le formulaire.');
            $this->redirect('transactions/create');
        }

        // Chargement des modèles
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/TypeOperation.php';
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/MouvementSolde.php';
        require_once APP_PATH . '/models/SeuilAlerte.php';
        require_once APP_PATH . '/models/AlerteSolde.php';
        require_once APP_PATH . '/models/CommissionConfig.php';
        require_once APP_PATH . '/models/CommissionTransaction.php';

        $txModel      = new Transaction();
        $typeModel    = new TypeOperation();
        $soldeModel   = new SoldeService();
        $mvtModel     = new MouvementSolde();
        $seuilModel   = new SeuilAlerte();
        $alerteModel  = new AlerteSolde();
        $configModel  = new CommissionConfig();
        $commModel    = new CommissionTransaction();

        try {
            $txModel->beginTransaction();

            // 1. Insérer la transaction
            $idTransaction = $txModel->create([
                'id_service' => $idService,
                'id_type'    => $idType,
                'id_user'    => Auth::id(),
                'montant'    => $montant,
                'reference'  => $reference,
                'note'       => $note,
                'statut'     => 'VALIDEE',
            ]);

            // 2. Récupérer les impacts du type d'opération
            $typeOp = $typeModel->find($idType);

            // 3. Mettre à jour Float si impacté
            if ($typeOp['impact_float'] !== 0) {
                $soldeFloat = $soldeModel->getSolde($idService, 'FLOAT');
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
                $soldeCaisse = $soldeModel->getSolde($idService, 'CAISSE');
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
            Session::flash('error', 'Erreur lors de l\'enregistrement. Veuillez réessayer plus tard.');
            $this->redirect('transactions/create');
        }
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
        $txModel = new Transaction();
        $txModel->update((int)$id, ['statut' => 'ANNULEE']);
        Session::flash('success', 'Transaction annulée.');
        $this->redirect('transactions/' . $id);
    }
}
