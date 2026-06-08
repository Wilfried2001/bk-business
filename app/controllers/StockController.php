<?php
// ============================================================
//  app/controllers/StockController.php — Fichier commenté
// ============================================================

// Classe StockController : implémente la logique métier pour cette partie de l’application
class StockController extends Controller {

// Méthode index : gère index. 
    public function index(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/AlerteSolde.php';

        $soldeModel  = new SoldeService();
        $alerteModel = new AlerteSolde();

        $this->render('stocks/index', [
            'soldes'  => $soldeModel->getAllAvecSeuils(),
            'alertes' => $alerteModel->getActives(),
        ], 'Gestion des stocks');
    }

// Méthode show : gère show. 
    public function show(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/MouvementSolde.php';
        require_once APP_PATH . '/models/SeuilAlerte.php';
        require_once APP_PATH . '/models/Service.php';

        $soldeModel   = new SoldeService();
        $mvtModel     = new MouvementSolde();
        $seuilModel   = new SeuilAlerte();
        $serviceModel = new Service();

        $service = $serviceModel->find((int)$id);
        if (!$service) $this->redirect('stocks');

        $this->render('stocks/show', [
            'service'        => $service,
            'soldes'         => $soldeModel->getByService((int)$id),
            'seuilHistories' => $seuilModel->getHistoryByService((int)$id),
        ], 'Stock — ' . $service['nom']);
    }

// Méthode saveThreshold : gère saveThreshold. 
    public function saveThreshold(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/SeuilAlerte.php';
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/Service.php';

        $this->verifyCsrf();

        $soldeModel  = new SoldeService();
        $seuilModel  = new SeuilAlerte();

        $service = (new Service())->find((int)$id);
        if (!$service) {
            $this->redirect('stocks');
        }

        $idSolde = isset($_POST['id_solde']) ? (int) $_POST['id_solde'] : 0;
        $valeurSeuil = isset($_POST['valeur_seuil']) ? trim($_POST['valeur_seuil']) : '';

        $normalizedSeuil = str_replace(',', '.', $valeurSeuil);
        if ($idSolde <= 0 || $valeurSeuil === '' || !is_numeric($normalizedSeuil)) {
            Session::flash('error', 'Veuillez renseigner une valeur de seuil valide.');
            $this->redirect('stocks/' . $id);
        }

        $solde = $soldeModel->find($idSolde);
        if (!$solde || (int)$solde['id_service'] !== (int)$service['id_service']) {
            Session::flash('error', 'Ce seuil ne correspond pas à ce service.');
            $this->redirect('stocks/' . $id);
        }

        $valeurSeuil = (float) str_replace(',', '.', $valeurSeuil);
        $seuilModel->saveForSolde($idSolde, $valeurSeuil, Auth::id());

        Session::flash('success', 'Seuil enregistré avec succès.');

        $redirectTo = isset($_POST['redirect_to']) ? trim($_POST['redirect_to']) : '';
        if ($redirectTo === '' || !$this->isInternalPath($redirectTo)) {
            $redirectTo = 'stocks/' . $id;
        }

        $this->redirect($redirectTo);
    }

// Méthode updateSolde : gère updateSolde. 
    public function updateSolde(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/Service.php';

        $this->verifyCsrf();

        $service = (new Service())->find((int)$id);
        if (!$service) {
            $this->redirect('stocks');
        }

        $idSolde = isset($_POST['id_solde']) ? (int) $_POST['id_solde'] : 0;
        $montant  = isset($_POST['montant_actuel']) ? trim($_POST['montant_actuel']) : '';
        $motif    = isset($_POST['motif']) ? trim($_POST['motif']) : '';

        $normalizedMontant = str_replace(',', '.', $montant);
        if ($idSolde <= 0 || $montant === '' || !is_numeric($normalizedMontant)) {
            Session::flash('error', 'Veuillez renseigner un montant valide.');
            $this->redirect('stocks/' . $id);
        }

        $montant = (float) $normalizedMontant;

        $soldeModel = new SoldeService();
        $solde = $soldeModel->find($idSolde);
        if (!$solde || (int)$solde['id_service'] !== (int)$service['id_service']) {
            Session::flash('error', 'Ce solde ne correspond pas à ce service.');
            $this->redirect('stocks/' . $id);
        }

        $soldeAvant = (float) $solde['montant_actuel'];
        $soldeApres  = $montant;

        // Mettre à jour le montant actuel
        $soldeModel->update($idSolde, ['montant_actuel' => $soldeApres]);

        // Journaliser l'ajustement via une transaction d'ajustement
        $diff = round(abs($soldeApres - $soldeAvant), 2);
        if ($diff > 0) {
            require_once APP_PATH . '/models/Transaction.php';
            require_once APP_PATH . '/models/MouvementSolde.php';
            require_once APP_PATH . '/models/TypeOperation.php';

            $txModel  = new Transaction();
            $mvtModel = new MouvementSolde();
            $typeModel = new TypeOperation();

            // Trouver ou créer le type 'AJUSTEMENT'
            $adjustType = $typeModel->queryOne("SELECT * FROM type_operation WHERE libelle = ?", ['AJUSTEMENT']);
            if (!$adjustType) {
                $idType = $typeModel->create([
                    'libelle' => 'AJUSTEMENT',
                    'description' => 'Transaction d\'ajustement manuel de solde',
                    'impact_float' => 0,
                    'impact_caisse' => 0,
                ]);
            } else {
                $idType = (int) $adjustType['id_type'];
            }

            $nature = $soldeApres > $soldeAvant ? 'CREDIT' : 'DEBIT';

            $idTransaction = $txModel->create([
                'id_service' => $solde['id_service'],
                'id_type'    => $idType,
                'id_user'    => Auth::id(),
                'montant'    => $diff,
                'reference'  => 'AJUSTEMENT',
                'note'       => 'Ajustement manuel de solde',
                'statut'     => 'VALIDEE',
            ]);

            $mvtModel->createMouvement(
                $idTransaction,
                $idSolde,
                $nature,
                $diff,
                $soldeAvant,
                $soldeApres,
                $motif ?: 'Ajustement manuel'
            );
        }

        Session::flash('success', 'Montant du solde mis à jour.');
        $this->redirect('stocks/' . $id);
    }

    // Formulaire global pour définir les stocks initiaux de chaque service
    public function defineForm(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/Service.php';
        require_once APP_PATH . '/models/SoldeService.php';

        $serviceModel = new Service();
        $soldeModel   = new SoldeService();

        $services = $serviceModel->getAllActifs();
        // Pour chaque service, récupérer ses soldes (FLOAT et CAISSE)
        foreach ($services as &$s) {
            $s['soldes'] = $soldeModel->getByService((int)$s['id_service']);
        }

        $this->render('stocks/define', [
            'services' => $services,
        ], 'Définir les stocks initiaux');
    }

    // Enregistrer les montants envoyés depuis le formulaire global
    public function defineStore(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/SoldeService.php';

        $this->verifyCsrf();

        $soldeModel = new SoldeService();

        $montants = $_POST['montant'] ?? [];
        $globalMotif = isset($_POST['global_motif']) ? trim($_POST['global_motif']) : '';
        if (empty($montants) || !is_array($montants)) {
            Session::flash('error', 'Aucun montant reçu.');
            $this->redirect('stocks/define');
        }

        $updated = 0;
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/MouvementSolde.php';
        require_once APP_PATH . '/models/TypeOperation.php';

        $txModel  = new Transaction();
        $mvtModel = new MouvementSolde();
        $typeModel = new TypeOperation();

        foreach ($montants as $idSolde => $val) {
            $idSolde = (int) $idSolde;
            $val = trim($val);
            if ($idSolde <= 0 || $val === '') continue;

            $normalizedVal = str_replace(',', '.', $val);
            if (!is_numeric($normalizedVal)) {
                continue;
            }
            $val = (float) $normalizedVal;
            $solde = $soldeModel->find($idSolde);
            if (!$solde) continue;

            $soldeAvant = (float) $solde['montant_actuel'];
            $soldeApres  = $val;
            if (round($soldeAvant,2) === round($soldeApres,2)) continue;

            // Mettre à jour
            $soldeModel->update($idSolde, ['montant_actuel' => $soldeApres]);

            // Création transaction d'ajustement et mouvement
            $diff = round(abs($soldeApres - $soldeAvant), 2);
            if ($diff > 0) {
                // Trouver ou créer type AJUSTEMENT
                $adjustType = $typeModel->queryOne("SELECT * FROM type_operation WHERE libelle = ?", ['AJUSTEMENT']);
                if (!$adjustType) {
                    $idType = $typeModel->create([
                        'libelle' => 'AJUSTEMENT',
                        'description' => 'Transaction d\'ajustement manuel de solde',
                        'impact_float' => 0,
                        'impact_caisse' => 0,
                    ]);
                } else {
                    $idType = (int) $adjustType['id_type'];
                }

                $nature = $soldeApres > $soldeAvant ? 'CREDIT' : 'DEBIT';

                $idTransaction = $txModel->create([
                    'id_service' => $solde['id_service'],
                    'id_type'    => $idType,
                    'id_user'    => Auth::id(),
                    'montant'    => $diff,
                    'reference'  => 'AJUSTEMENT',
                    'note'       => 'Ajustement manuel de solde',
                    'statut'     => 'VALIDEE',
                ]);

                $mvtModel->createMouvement(
                    $idTransaction,
                    $idSolde,
                    $nature,
                    $diff,
                    $soldeAvant,
                    $soldeApres,
                    $globalMotif ?: 'Ajustement initial'
                );
            }

            $updated++;
        }

        if ($updated > 0) {
            Session::flash('success', "Stocks mis à jour pour {$updated} soldes.");
        } else {
            Session::flash('info', 'Aucun changement appliqué.');
        }

        $this->redirect('stocks');
    }

    // Formulaire global pour définir les seuils d'alerte pour tous les services
    public function seuilsForm(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/Service.php';
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/SeuilAlerte.php';

        $serviceModel = new Service();
        $soldeModel   = new SoldeService();
        $seuilModel   = new SeuilAlerte();

        $services = $serviceModel->getAllActifs();
        // Pour chaque service, récupérer ses soldes (FLOAT et CAISSE) avec leurs seuils
        foreach ($services as &$s) {
            $s['soldes'] = $soldeModel->getByService((int)$s['id_service']);
        }

        $this->render('stocks/seuils', [
            'services' => $services,
        ], 'Gérer les seuils d\'alerte');
    }

    // Enregistrer les seuils d'alerte envoyés depuis le formulaire global
    public function seuilsSave(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/SeuilAlerte.php';
        require_once APP_PATH . '/models/SoldeService.php';

        $this->verifyCsrf();

        $seuilModel = new SeuilAlerte();
        $soldeModel = new SoldeService();

        $seuils = $_POST['seuil'] ?? [];
        if (empty($seuils) || !is_array($seuils)) {
            Session::flash('error', 'Aucun seuil reçu.');
            $this->redirect('stocks/seuils/all');
        }

        $updated = 0;
        foreach ($seuils as $idSolde => $val) {
            $idSolde = (int) $idSolde;
            $val = trim($val ?? '');

            // Si vide, on le traite comme si l'utilisateur veut créer/mettre à jour un seuil vide
            if ($idSolde <= 0) continue;

            // Vérifier que le solde existe
            $solde = $soldeModel->find($idSolde);
            if (!$solde) continue;

            // Si la valeur n'est pas vide et valide, on la sauvegarde
            if ($val !== '') {
                $normalizedValue = str_replace(',', '.', $val);
                if (!is_numeric($normalizedValue)) {
                    continue;
                }
                $valeur = (float) $normalizedValue;
                $seuilModel->saveForSolde($idSolde, $valeur, Auth::id());
                $updated++;
            }
        }

        if ($updated > 0) {
            Session::flash('success', "Seuils d'alerte mis à jour pour {$updated} soldes.");
        } else {
            Session::flash('info', 'Aucun changement appliqué.');
        }

        $this->redirect('stocks/seuils/all');
    }
}