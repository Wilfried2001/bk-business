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
        $reportType   = $this->get('report_type', 'monthly');
        $reportDate   = $this->get('date', date('Y-m-d'));
        $mois         = (int)($this->get('mois') ?: date('m'));
        $annee        = (int)($this->get('annee') ?: date('Y'));
        $idService    = (int)$this->get('service');

        $period = $this->resolveReportPeriod($reportType, $reportDate, $mois, $annee);

        $this->render('rapports/index', [
            'transactions' => $txModel->getAllWithDetails([
                'date_debut'         => $period['debut'],
                'date_fin'           => $period['fin'],
                'id_service'         => $idService,
                'exclude_adjustments' => true,
            ]),
            'benefices'   => Auth::hasRole(['COMPTABLE','DG'])
                            ? $commModel->getBeneficesParService($mois, $annee, $idService)
                            : [],
            'services'    => $serviceModel->getAllActifs(),
            'mois'        => $mois,
            'annee'       => $annee,
            'report_type' => $reportType,
            'report_date' => $reportDate,
            'period'      => $period,
            'filtres'     => ['id_service' => $idService],
        ], 'Rapports');
    }

    private function resolveReportPeriod(string $reportType, string $reportDate, int $mois, int $annee): array {
        $timestamp = strtotime($reportDate);
        if ($timestamp === false) {
            $timestamp = time();
        }

        if ($reportType === 'daily') {
            $date = date('Y-m-d', $timestamp);
            return [
                'debut' => $date,
                'fin' => $date,
                'label' => 'Journée du ' . date('d/m/Y', $timestamp),
            ];
        }

        if ($reportType === 'weekly') {
            $weekDay = (int)date('N', $timestamp);
            $monday = strtotime('-' . ($weekDay - 1) . ' days', $timestamp);
            $sunday = strtotime('+' . (7 - $weekDay) . ' days', $timestamp);
            return [
                'debut' => date('Y-m-d', $monday),
                'fin' => date('Y-m-d', $sunday),
                'label' => 'Semaine du ' . date('d/m/Y', $monday) . ' au ' . date('d/m/Y', $sunday),
            ];
        }

        if ($reportType === 'annual') {
            return [
                'debut' => sprintf('%04d-01-01', $annee),
                'fin' => sprintf('%04d-12-31', $annee),
                'label' => 'Année ' . $annee,
            ];
        }

        return [
            'debut' => sprintf('%04d-%02d-01', $annee, $mois),
            'fin' => date('Y-m-t', mktime(0, 0, 0, $mois, 1, $annee)),
            'label' => sprintf('Mois %02d/%04d', $mois, $annee),
        ];
    }

// Méthode export : gère export. 
    public function export(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/Transaction.php';

        $mois       = (int)($this->get('mois') ?: date('m'));
        $annee      = (int)($this->get('annee') ?: date('Y'));
        $serviceId  = (int)$this->get('service');
        $reportType = $this->get('report_type', 'monthly');
        $reportDate = $this->get('date', date('Y-m-d'));
        $format     = strtolower($this->get('format', 'csv'));

        $errors = [];
        if ($mois < 1 || $mois > 12) {
            $errors[] = 'Le mois sélectionné est invalide.';
        }
        if ($annee < 2020 || $annee > (int)date('Y') + 1) {
            $errors[] = 'L\'année sélectionnée est invalide.';
        }
        if (!in_array($format, ['csv', 'json', 'html', 'xls', 'pdf'], true)) {
            $errors[] = 'Le format d\'export demandé est invalide.';
        }
        if (!in_array($reportType, ['daily', 'weekly', 'monthly', 'annual'], true)) {
            $errors[] = 'Le type de rapport demandé est invalide.';
        }
        if (strtotime($reportDate) === false) {
            $errors[] = 'La date du rapport est invalide.';
        }

        if (!empty($errors)) {
            Session::flash('error', implode(' ', $errors));
            $this->redirect('rapports');
        }

        $period = $this->resolveReportPeriod($reportType, $reportDate, $mois, $annee);

        $transactions = (new Transaction())->getAllWithDetails([
            'date_debut'         => $period['debut'],
            'date_fin'           => $period['fin'],
            'id_service'         => $serviceId,
            'exclude_adjustments' => true,
        ]);

        $filename = sprintf('rapport_%s_%s.%s', $reportType, str_replace('-', '', $period['debut']), $format === 'xls' ? 'xls' : $format);
        $rows = [];
        $totalMontant = 0;

        foreach ($transactions as $tx) {
            $rows[] = [
                'id' => $tx['id_transaction'],
                'date' => formatDate($tx['date_heure']),
                'service' => $tx['nom_service'],
                'type' => $tx['libelle_type'],
                'montant' => formatMontant((float)$tx['montant']),
                'agent' => $tx['nom_agent'],
                'statut' => $tx['statut'],
            ];
            $totalMontant += (float)$tx['montant'];
        }

        switch ($format) {
            case 'json':
                header('Content-Type: application/json; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                echo json_encode(['meta' => [
                    'periode' => ['mois' => $mois, 'annee' => $annee, 'service' => $serviceId],
                    'total_transactions' => count($rows),
                    'total_montant' => $totalMontant,
                ], 'data' => $rows], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;

            case 'pdf':
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $html = $this->renderReportTable($rows, $totalMontant, false);
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                echo $dompdf->output();
                exit;

            case 'xls':
            case 'html':
                header('Content-Type: ' . ($format === 'xls' ? 'application/vnd.ms-excel' : 'text/html; charset=utf-8'));
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                echo $this->renderReportTable($rows, $totalMontant, $format === 'xls');
                exit;

            case 'csv':
            default:
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['ID', 'Date', 'Service', 'Type', 'Montant', 'Agent', 'Statut'], ';');
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['id'],
                        $row['date'],
                        $this->sanitizeCsvField($row['service']),
                        $this->sanitizeCsvField($row['type']),
                        $row['montant'],
                        $this->sanitizeCsvField($row['agent']),
                        $this->sanitizeCsvField($row['statut']),
                    ], ';');
                }
                fclose($output);
                exit;
        }
    }

    private function renderReportTable(array $rows, float $totalMontant, bool $forExcel = false): string {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>table,th,td{border:1px solid #ccc;border-collapse:collapse;padding:6px 8px;font-family:Arial,sans-serif;}th{background:#f4f4f4;}</style></head><body>';
        $html .= '<table><thead><tr><th>ID</th><th>Date</th><th>Service</th><th>Type</th><th>Montant</th><th>Agent</th><th>Statut</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>' .
                '<td>' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '</td>' .
                '<td>' . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . '</td>' .
                '<td>' . htmlspecialchars($row['service'], ENT_QUOTES, 'UTF-8') . '</td>' .
                '<td>' . htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8') . '</td>' .
                '<td style="text-align:right;">' . htmlspecialchars($row['montant'], ENT_QUOTES, 'UTF-8') . '</td>' .
                '<td>' . htmlspecialchars($row['agent'], ENT_QUOTES, 'UTF-8') . '</td>' .
                '<td>' . htmlspecialchars($row['statut'], ENT_QUOTES, 'UTF-8') . '</td>' .
            '</tr>';
        }
        $html .= '</tbody><tfoot><tr><td colspan="4"><strong>Total</strong></td><td style="text-align:right;"><strong>' . htmlspecialchars(formatMontant($totalMontant), ENT_QUOTES, 'UTF-8') . '</strong></td><td colspan="2"></td></tr></tfoot></table>';
        $html .= '</body></html>';
        return $html;
    }

    private function sanitizeCsvField(string $value): string {
        if ($value === '') {
            return $value;
        }
        if (in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'" . $value;
        }
        return $value;
    }
}
