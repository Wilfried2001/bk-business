<?php
// ============================================================
//  app/controllers/RapportController.php — Fichier commenté
// ============================================================

// Classe RapportController : implémente la logique métier pour cette partie de l’application
class RapportController extends Controller {

// Méthode index : gère index. 
    public function index(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/CommissionTransaction.php';
        require_once APP_PATH . '/models/Service.php';

        $txModel      = new Transaction();
        $commModel    = new CommissionTransaction();
        $serviceModel = new Service();
        $mois  = (int)($this->get('mois')  ?: date('m'));
        $annee = (int)($this->get('annee') ?: date('Y'));
        $idService = (int)$this->get('service');

        $this->render('rapports/index', [
            'transactions' => $txModel->getAllWithDetails([
                'date_debut'  => "{$annee}-{$mois}-01",
                'date_fin'    => date('Y-m-t', mktime(0,0,0,$mois,1,$annee)),
                'id_service'  => $idService,
            ]),
            'benefices'   => Auth::hasRole(['COMPTABLE','DG'])
                            ? $commModel->getBeneficesParService($mois, $annee, $idService)
                            : [],
            'services'    => $serviceModel->getAllActifs(),
            'mois'        => $mois,
            'annee'       => $annee,
            'filtres'     => ['id_service' => $idService],
        ], 'Rapports');
    }

// Méthode export : gère export. 
    public function export(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        // Export CSV simple
        require_once APP_PATH . '/models/Transaction.php';
        $txModel = new Transaction();
        $mois    = (int)($this->get('mois')  ?: date('m'));
        $annee   = (int)($this->get('annee') ?: date('Y'));

        $transactions = $txModel->getAllWithDetails([
            'date_debut' => "{$annee}-{$mois}-01",
            'date_fin'   => date('Y-m-t', mktime(0,0,0,$mois,1,$annee)),
            'id_service' => (int)$this->get('service'),
        ]);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rapport_' . $annee . '_' . $mois . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Date','Service','Type','Montant','Agent','Statut'], ';');
        foreach ($transactions as $tx) {
            fputcsv($out, [
                $tx['id_transaction'],
                $tx['date_heure'],
                $tx['nom_service'],
                $tx['libelle_type'],
                $tx['montant'],
                $tx['nom_agent'],
                $tx['statut'],
            ], ';');
        }
        fclose($out);
        exit;
    }
}
