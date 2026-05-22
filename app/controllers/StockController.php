<?php
class StockController extends Controller {

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

    public function show(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/MouvementSolde.php';
        require_once APP_PATH . '/models/Service.php';

        $soldeModel   = new SoldeService();
        $mvtModel     = new MouvementSolde();
        $serviceModel = new Service();

        $service = $serviceModel->find((int)$id);
        if (!$service) $this->redirect('stocks');

        $this->render('stocks/show', [
            'service' => $service,
            'soldes'  => $soldeModel->getByService((int)$id),
        ], 'Stock — ' . $service['nom']);
    }

    public function saveThreshold(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/SeuilAlerte.php';
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/Service.php';

        $soldeModel  = new SoldeService();
        $seuilModel  = new SeuilAlerte();

        $service = (new Service())->find((int)$id);
        if (!$service) {
            $this->redirect('stocks');
        }

        $idSolde = isset($_POST['id_solde']) ? (int) $_POST['id_solde'] : 0;
        $valeurSeuil = isset($_POST['valeur_seuil']) ? trim($_POST['valeur_seuil']) : '';

        if ($idSolde <= 0 || $valeurSeuil === '') {
            $this->redirect('stocks/' . $id);
        }

        $solde = $soldeModel->find($idSolde);
        if (!$solde || (int)$solde['id_service'] !== (int)$service['id_service']) {
            $this->redirect('stocks/' . $id);
        }

        $valeurSeuil = (float) str_replace(',', '.', $valeurSeuil);
        $seuilModel->saveForSolde($idSolde, $valeurSeuil);

        $redirectTo = isset($_POST['redirect_to']) && trim($_POST['redirect_to']) !== ''
            ? trim($_POST['redirect_to'])
            : 'stocks/' . $id;

        $this->redirect($redirectTo . '?success=seuil_enregistre');
    }
}
