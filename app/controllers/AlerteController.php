<?php
class AlerteController extends Controller {

    public function index(): void {
        Auth::requireRole(['SUPERVISEUR', 'DG']);
        require_once APP_PATH . '/models/AlerteSolde.php';

        $alerteModel = new AlerteSolde();
        $this->render('alertes/index', [
            'alertes' => $alerteModel->getActives(),
        ], 'Alertes de stock');
    }

    public function traiter(string $id): void {
        Auth::requireRole(['SUPERVISEUR', 'DG']);
        $this->verifyCsrf();
        require_once APP_PATH . '/models/AlerteSolde.php';

        $alerteModel = new AlerteSolde();
        $alerteModel->traiter((int)$id, Auth::id());
        Session::flash('success', 'Alerte marquée comme traitée.');
        $this->redirect('alertes');
    }
}
