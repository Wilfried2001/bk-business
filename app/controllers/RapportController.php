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
                'date_debut'        => "{$annee}-{$mois}-01",
                'date_fin'          => date('Y-m-t', mktime(0,0,0,$mois,1,$annee)),
                'id_service'        => $idService,
                'exclude_adjustments' => true,
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
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/CommissionTransaction.php';
        require_once APP_PATH . '/models/Service.php';

        $txModel      = new Transaction();
        $commModel    = new CommissionTransaction();
        $serviceModel = new Service();
        $mois         = (int)($this->get('mois') ?: date('m'));
        $annee        = (int)($this->get('annee') ?: date('Y'));
        $idService    = (int)$this->get('service');
        $format       = strtolower($this->get('format', 'csv'));
        $allowedFormats = ['csv', 'pdf', 'json', 'html', 'xlsx'];

        if (!in_array($format, $allowedFormats, true)) {
            $format = 'csv';
        }

        $transactions = $txModel->getAllWithDetails([
            'date_debut'        => "{$annee}-{$mois}-01",
            'date_fin'          => date('Y-m-t', mktime(0,0,0,$mois,1,$annee)),
            'id_service'        => $idService,
            'exclude_adjustments' => true,
        ]);

        $benefices = Auth::hasRole(['COMPTABLE', 'DG'])
            ? $commModel->getBeneficesParService($mois, $annee, $idService)
            : [];
        $services = $serviceModel->getAllActifs();

        $reportData = [
            'mois' => $mois,
            'annee' => $annee,
            'id_service' => $idService,
            'transactions' => $transactions,
            'benefices' => $benefices,
            'services' => $services,
            'generated_at' => date('Y-m-d H:i:s'),
        ];

        switch ($format) {
            case 'pdf':
                $this->exportPdf($reportData);
                break;
            case 'json':
                $this->exportJson($reportData);
                break;
            case 'html':
                $this->exportHtml($reportData);
                break;
            case 'xlsx':
                $this->exportXlsx($reportData);
                break;
            case 'csv':
            default:
                $this->exportCsv($reportData);
                break;
        }
    }

    private function exportCsv(array $reportData): void {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->buildFilename($reportData, 'csv') . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Date', 'Service', 'Type', 'Montant', 'Agent', 'Statut'], ';');
        foreach ($reportData['transactions'] as $tx) {
            $safeService = $this->sanitizeCsvField($tx['nom_service']);
            $safeType = $this->sanitizeCsvField($tx['libelle_type']);
            $safeAgent = $this->sanitizeCsvField($tx['nom_agent']);
            fputcsv($out, [
                $tx['id_transaction'],
                $tx['date_heure'],
                $safeService,
                $safeType,
                $tx['montant'],
                $safeAgent,
                $tx['statut'],
            ], ';');
        }
        fclose($out);
        exit;
    }

    private function exportJson(array $reportData): void {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->buildFilename($reportData, 'json') . '"');
        echo json_encode([
            'periode' => [
                'mois' => $reportData['mois'],
                'annee' => $reportData['annee'],
                'service_id' => $reportData['id_service'],
            ],
            'generated_at' => $reportData['generated_at'],
            'transactions' => $reportData['transactions'],
            'benefices' => $reportData['benefices'],
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function exportHtml(array $reportData): void {
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->buildFilename($reportData, 'html') . '"');
        echo $this->buildHtmlReport($reportData);
        exit;
    }

    private function exportXlsx(array $reportData): void {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->buildFilename($reportData, 'xls') . '"');

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\">\n";
        echo "<Worksheet ss:Name=\"Rapport\">\n";
        echo "<Table>\n";
        echo "<Row><Cell><Data ss:Type=\"String\">ID</Data></Cell><Cell><Data ss:Type=\"String\">Date</Data></Cell><Cell><Data ss:Type=\"String\">Service</Data></Cell><Cell><Data ss:Type=\"String\">Type</Data></Cell><Cell><Data ss:Type=\"String\">Montant</Data></Cell><Cell><Data ss:Type=\"String\">Agent</Data></Cell><Cell><Data ss:Type=\"String\">Statut</Data></Cell></Row>\n";
        foreach ($reportData['transactions'] as $tx) {
            echo '<Row>';
            echo '<Cell><Data ss:Type="Number">' . (int)$tx['id_transaction'] . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . $this->escapeXml((string)$tx['date_heure']) . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . $this->escapeXml((string)$tx['nom_service']) . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . $this->escapeXml((string)$tx['libelle_type']) . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . $this->escapeXml((string)$tx['montant']) . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . $this->escapeXml((string)$tx['nom_agent']) . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . $this->escapeXml((string)$tx['statut']) . '</Data></Cell>';
            echo '</Row>\n';
        }
        echo "</Table>\n";
        echo "</Worksheet>\n";
        echo "</Workbook>\n";
        exit;
    }

    private function exportPdf(array $reportData): void {
        if (!class_exists('Dompdf\\Dompdf')) {
            $this->exportHtml($reportData);
            return;
        }

        $html = $this->buildHtmlReport($reportData);
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $this->buildFilename($reportData, 'pdf') . '"');
        echo $dompdf->output();
        exit;
    }

    private function buildHtmlReport(array $reportData): string {
        $totalMontant = 0;
        foreach ($reportData['transactions'] as $tx) {
            $totalMontant += (float)$tx['montant'];
        }

        ob_start();
        ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport BK Business</title>
    <style>
        body { font-family: Arial, sans-serif; color: #0f172a; }
        h1 { margin-bottom: 6px; }
        .meta { color: #475569; font-size: 12px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 12px; text-align: left; }
        th { background: #f8fafc; }
        .summary { margin: 12px 0; }
    </style>
</head>
<body>
    <h1>Rapport de transactions</h1>
    <div class="meta">Période: <?= htmlspecialchars((string)$reportData['mois'], ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars((string)$reportData['annee'], ENT_QUOTES, 'UTF-8') ?> · Généré le <?= htmlspecialchars((string)$reportData['generated_at'], ENT_QUOTES, 'UTF-8') ?></div>
    <div class="summary">Transactions: <?= count($reportData['transactions']) ?> · Montant total: <?= htmlspecialchars(formatMontant($totalMontant), ENT_QUOTES, 'UTF-8') ?></div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Service</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Agent</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportData['transactions'] as $tx): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$tx['id_transaction'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)$tx['date_heure'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)$tx['nom_service'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)$tx['libelle_type'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(formatMontant((float)$tx['montant']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)$tx['nom_agent'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)$tx['statut'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
        <?php
        return ob_get_clean();
    }

    private function buildFilename(array $reportData, string $extension): string {
        return 'rapport_' . $reportData['annee'] . '_' . $reportData['mois'] . '.' . $extension;
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

    private function escapeXml(string $value): string {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
