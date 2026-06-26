<?php
// ============================================================
//  app/controllers/DashboardController.php — Fichier commenté
// ============================================================

// Classe DashboardController : implémente la logique métier pour cette partie de l’application
class DashboardController extends Controller {

// Méthode index : gère index. 
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

        $nbHier = (int)($txModel->queryOne("
            SELECT COUNT(*) AS nb
            FROM transaction t
            JOIN type_operation to2 ON to2.id_type = t.id_type
            WHERE DATE(t.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
              AND t.statut = 'VALIDEE'
              AND to2.libelle != 'AJUSTEMENT'
        ")['nb'] ?? 0);

        $totalHier = (float)($txModel->queryOne("
            SELECT COALESCE(SUM(t.montant), 0) AS total
            FROM transaction t
            JOIN type_operation to2 ON to2.id_type = t.id_type
            WHERE DATE(t.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
              AND t.statut = 'VALIDEE'
              AND to2.libelle != 'AJUSTEMENT'
        ")['total'] ?? 0);

        $nbTransactionsJour = $txModel->getNbJour();
        $totalMontantJour = $txModel->getTotalJour();
        $soldes = $soldeModel->getAllAvecSeuils();
        $alertesActives = $alerteModel->getActives();

        $data = [
            'nbTransactionsJour'   => $nbTransactionsJour,
            'totalMontantJour'     => $totalMontantJour,
            'nbAlertesActives'     => $alerteModel->compterActives(),
            'alertesActives'       => $this->prioriserAlertes($alertesActives),
            'soldes'               => $soldes,
            'soldesParService'     => $this->grouperSoldesParService($soldes),
            'variationTransactionsJour' => $this->variationPourcentage($nbTransactionsJour, $nbHier),
            'variationMontantJour' => $this->variationPourcentage($totalMontantJour, $totalHier),
            'dernièresTransactions' => $txModel->getAllWithDetails([
                'limit' => 10,
                'exclude_adjustments' => true,
            ]),
        ];

        // Données commission uniquement pour Comptable et DG
        if (Auth::hasRole(['COMPTABLE', 'DG'])) {
            $data['totalCommissionsMois']  = $commModel->getTotalCommissions();
            $data['beneficesParService']   = $commModel->getBeneficesParService();
            $data['topProfitServices']     = $commModel->getTopServicesByCommission(5);
            $data['rentabiliteMois']       = $this->calculerRentabilite($txModel, (float)$data['totalCommissionsMois']);
        }

        $data['topServicesUsage']      = $txModel->getTopServicesByUsage(5);
        $data['topServicesMontant']    = $txModel->getTopServicesByMontant(5);
        $data['topAlertServices']      = $alerteModel->getTopServicesByAlertCount(5);

        // Données graphiques : transactions des 30 derniers jours
        $rows = $txModel->query(
            "SELECT DATE(t.created_at) AS day, COUNT(*) AS cnt
             FROM transaction t
             JOIN type_operation to2 ON to2.id_type = t.id_type
             WHERE DATE(t.created_at) >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
               AND t.statut = 'VALIDEE'
               AND to2.libelle != 'AJUSTEMENT'
             GROUP BY day
             ORDER BY day ASC"
        );

        $labels = [];
        $counts = [];
        for ($i = 29; $i >= 0; $i--) {
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

        if (Auth::hasRole(['COMPTABLE', 'DG'])) {
            $commissionRows = $commModel->query("
                SELECT DATE(ct.date_calcul) AS day, COALESCE(SUM(ct.montant_commission), 0) AS total
                FROM commission_transaction ct
                WHERE DATE(ct.date_calcul) >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
                GROUP BY day
                ORDER BY day ASC
            ");

            $commissionByDay = [];
            foreach ($labels as $index => $label) {
                $d = date('Y-m-d', strtotime('-' . (29 - $index) . ' days'));
                $commissionByDay[$d] = 0;
            }
            foreach ($commissionRows as $row) {
                if (isset($commissionByDay[$row['day']])) {
                    $commissionByDay[$row['day']] = (float)$row['total'];
                }
            }
            $data['chartCommissionsDaily'] = [
                'labels' => $labels,
                'data' => array_values($commissionByDay),
            ];
        } else {
            $data['chartCommissionsDaily'] = ['labels' => [], 'data' => []];
        }

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

    private function variationPourcentage(float $actuel, float $precedent): ?float {
        if ($precedent <= 0) {
            return $actuel > 0 ? 100.0 : null;
        }
        return round((($actuel - $precedent) / $precedent) * 100, 1);
    }

    private function calculerRentabilite(Transaction $txModel, float $totalCommissions): array {
        $volumeMois = (float)($txModel->queryOne("
            SELECT COALESCE(SUM(t.montant), 0) AS total
            FROM transaction t
            JOIN type_operation to2 ON to2.id_type = t.id_type
            WHERE MONTH(t.created_at) = MONTH(CURDATE())
              AND YEAR(t.created_at) = YEAR(CURDATE())
              AND t.statut = 'VALIDEE'
              AND to2.libelle != 'AJUSTEMENT'
        ")['total'] ?? 0);

        return [
            'volume' => $volumeMois,
            'commission' => $totalCommissions,
            'taux' => $volumeMois > 0 ? round(($totalCommissions / $volumeMois) * 100, 1) : 0,
            'objectif' => 15,
        ];
    }

    private function grouperSoldesParService(array $soldes): array {
        $services = [];
        foreach ($soldes as $solde) {
            $id = (int)$solde['id_service'];
            if (!isset($services[$id])) {
                $services[$id] = [
                    'id_service' => $id,
                    'nom_service' => $solde['nom_service'],
                    'categorie' => $solde['categorie'],
                    'float' => null,
                    'caisse' => null,
                    'en_alerte' => false,
                ];
            }

            $type = strtolower($solde['type_solde']);
            $services[$id][$type] = $solde;
            if (!empty($solde['en_alerte'])) {
                $services[$id]['en_alerte'] = true;
            }
        }

        usort($services, function ($a, $b) {
            return ($b['en_alerte'] <=> $a['en_alerte']) ?: strcmp($a['nom_service'], $b['nom_service']);
        });

        return $services;
    }

    private function prioriserAlertes(array $alertes): array {
        foreach ($alertes as &$alerte) {
            $seuil = (float)($alerte['valeur_seuil'] ?? 0);
            $montant = (float)($alerte['montant_actuel'] ?? 0);
            $alerte['ecart_seuil'] = max(0, $seuil - $montant);
            $alerte['niveau_restant'] = $seuil > 0 ? round(($montant / $seuil) * 100) : 0;
            $alerte['criticite'] = $alerte['niveau_restant'] < 50 ? 'critique' : 'attention';
        }
        unset($alerte);

        usort($alertes, function ($a, $b) {
            return ($b['ecart_seuil'] <=> $a['ecart_seuil']);
        });

        return $alertes;
    }
}
