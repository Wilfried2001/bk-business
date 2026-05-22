<?php
class DashboardController extends Controller {

    public function index(): void {
        Auth::requireAuth();

        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/SoldeService.php';
        require_once APP_PATH . '/models/AlerteSolde.php';
        require_once APP_PATH . '/models/CommissionTransaction.php';

        $txModel      = new Transaction();
        $soldeModel   = new SoldeService();
        $alerteModel  = new AlerteSolde();
        $commModel    = new CommissionTransaction();

        $data = [
            'nbTransactionsJour'  => $txModel->getNbJour(),
            'totalMontantJour'    => $txModel->getTotalJour(),
            'nbAlertesActives'    => $alerteModel->compterActives(),
            'alertesActives'      => $alerteModel->getActives(),
            'soldes'              => $soldeModel->getAllAvecSeuils(),
            'dernièresTransactions' => $txModel->getAllWithDetails(['limit' => 10]),
        ];

        // Données commission uniquement pour Comptable et DG
        if (Auth::hasRole(['COMPTABLE', 'DG'])) {
            $data['totalCommissionsMois'] = $commModel->getTotalCommissions();
            $data['beneficesParService']  = $commModel->getBeneficesParService();
        }

        // Données graphiques : transactions des 7 derniers jours
        $rows = $txModel->query(
            "SELECT DATE(date_heure) AS day, COUNT(*) AS cnt
             FROM transaction
             WHERE DATE(date_heure) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND statut = 'VALIDEE'
             GROUP BY day
             ORDER BY day ASC"
        );

        $labels = [];
        $counts = [];
        // initialize last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d/m', strtotime($d));
            $counts[$d] = 0;
        }

        foreach ($rows as $r) {
            $day = $r['day'];
            if (isset($counts[$day])) {
                $counts[$day] = (int)$r['cnt'];
            }
        }

        $data['chartTransactions'] = [
            'labels' => $labels,
            'data'   => array_values($counts),
        ];

        // Données commissions par service (si dispo)
        if (!empty($data['beneficesParService'])) {
            $labelsC = [];
            $dataC = [];
            foreach ($data['beneficesParService'] as $b) {
                $labelsC[] = $b['nom_service'];
                $dataC[]   = (float)$b['total_commission'];
            }
            $data['chartCommissions'] = ['labels' => $labelsC, 'data' => $dataC];
        } else {
            $data['chartCommissions'] = ['labels' => [], 'data' => []];
        }

        $this->render('dashboard/index', $data, 'Tableau de bord');
    }
}
