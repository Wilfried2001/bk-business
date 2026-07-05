<?php
// ============================================================
//  app/controllers/TransfertFloatController.php
// ============================================================

class TransfertFloatController extends Controller {

    public function index(): void {
        Auth::requireAuth();
        require_once APP_PATH . '/models/TransfertFloat.php';
        require_once APP_PATH . '/models/Service.php';

        $transferModel = new TransfertFloat();
        $serviceModel = new Service();

        $filtres = [];
        if (!Auth::hasRole(['SUPERVISEUR', 'DG'])) {
            $filtres['id_demande_par'] = Auth::id();
        }
        $agencyId = AgencyContext::resolveAgencyId();
        if ($agencyId !== null) {
            $filtres['id_agence'] = $agencyId;
        }

        $this->render('transferts_float/index', [
            'transferts' => $transferModel->getAll($filtres),
            'services'   => $serviceModel->getAllActifs(),
        ], 'Transferts de float');
    }

    public function create(): void {
        Auth::requireRole(['AGENT', 'SUPERVISEUR', 'DG']);
        require_once APP_PATH . '/models/Service.php';
        require_once APP_PATH . '/models/Agence.php';

        $serviceModel = new Service();
        $agenceModel = new Agence();

        $this->render('transferts_float/create', [
            'services' => $serviceModel->getAllActifs(),
            'agences'  => $agenceModel->getAllActives(),
        ], 'Demande de transfert de float');
    }

    public function store(): void {
        Auth::requireRole(['AGENT', 'SUPERVISEUR', 'DG']);
        $this->verifyCsrf();

        $idSource = (int)$this->post('id_agence_source');
        $idDestination = (int)$this->post('id_agence_destination');
        $idService = (int)$this->post('id_service');
        $montant = trim((string)$this->post('montant'));
        $motif = trim((string)$this->post('motif', ''));

        $normalizedMontant = str_replace(',', '.', $montant);

        $errors = [];
        if ($idSource <= 0 || $idDestination <= 0) {
            $errors[] = 'Veuillez sélectionner les deux agences.';
        }
        if ($idSource === $idDestination) {
            $errors[] = 'L’agence source et l’agence destination doivent être différentes.';
        }
        if ($idService <= 0) {
            $errors[] = 'Veuillez sélectionner un service.';
        }
        if ($normalizedMontant === '' || !is_numeric($normalizedMontant) || (float)$normalizedMontant <= 0) {
            $errors[] = 'Veuillez saisir un montant valide.';
        }
        if (mb_strlen($motif, 'UTF-8') > 255) {
            $errors[] = 'Le motif doit comporter au maximum 255 caractères.';
        }

        if (!empty($errors)) {
            Session::flash('error', implode(' ', $errors));
            $this->redirect('transferts-float/create');
        }

        require_once APP_PATH . '/models/TransfertFloat.php';

        $transferModel = new TransfertFloat();
        $transferModel->createRequest([
            'id_agence_source'      => $idSource,
            'id_agence_destination' => $idDestination,
            'id_service'            => $idService,
            'id_demande_par'        => Auth::id(),
            'montant'               => (float)$normalizedMontant,
            'motif'                 => $motif ?: null,
        ]);

        Session::flash('success', 'Demande de transfert de float enregistrée avec succès.');
        $this->redirect('transferts-float');
    }

    public function show(string $id): void {
        Auth::requireAuth();
        require_once APP_PATH . '/models/TransfertFloat.php';
        require_once APP_PATH . '/models/MouvementSoldeAgence.php';

        $transferModel = new TransfertFloat();
        $mvtModel = new MouvementSoldeAgence();

        $transfer = $transferModel->getById((int)$id);
        if (!$transfer) {
            $this->redirect('transferts-float');
        }

        if (!Auth::hasRole(['SUPERVISEUR', 'DG']) && $transfer['id_demande_par'] !== Auth::id()) {
            $this->redirect('transferts-float');
        }

        $this->render('transferts_float/show', [
            'transfert' => $transfer,
            'mouvements' => $mvtModel->getByTransfert((int)$id),
        ], 'Détail du transfert #' . $id);
    }

    public function approve(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'DG']);
        $this->verifyCsrf();

        require_once APP_PATH . '/models/TransfertFloat.php';

        $transferModel = new TransfertFloat();
        try {
            $transferModel->executeTransfer((int)$id, Auth::id());
            Session::flash('success', 'Transfert de float validé et exécuté avec succès.');
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('transferts-float/' . $id);
    }

    public function reject(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'DG']);
        $this->verifyCsrf();

        $commentaire = trim((string)$this->post('commentaire', ''));
        if ($commentaire === '') {
            Session::flash('error', 'Veuillez saisir un commentaire pour le refus.');
            $this->redirect('transferts-float/' . $id);
        }

        require_once APP_PATH . '/models/TransfertFloat.php';

        $transferModel = new TransfertFloat();
        try {
            $transferModel->reject((int)$id, Auth::id(), $commentaire);
            Session::flash('success', 'Transfert de float refusé avec succès.');
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('transferts-float/' . $id);
    }
}
