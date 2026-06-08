<?php
// ============================================================
//  app/controllers/AlerteController.php — Fichier commenté
// ============================================================

// Classe AlerteController : implémente la logique métier pour cette partie de l’application
class AlerteController extends Controller {

// Méthode index : gère index. 
    public function index(): void {
        Auth::requireRole(['SUPERVISEUR', 'DG']);
        require_once APP_PATH . '/models/AlerteSolde.php';

        $alerteModel = new AlerteSolde();
        $this->render('alertes/index', [
            'alertes' => $alerteModel->getActives(),
        ], 'Alertes de stock');
    }

// Méthode traiter : gère traiter. 
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
