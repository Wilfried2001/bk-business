<?php
// ============================================================
//  app/controllers/AgentIAController.php — Agent IA BK_Business
//  Intègre Claude pour analyser les données en temps réel
// ============================================================

class AgentIAController extends Controller {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Affiche l'interface de chat IA
     */
    public function index(): void {
        Auth::requireRole(['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG']);
        $this->render('agent/chat', [], 'Assistant IA');
    }

    /**
     * Endpoint API : reçoit la question, retourne la réponse IA
     * POST /api/agent/ask
     */
    public function ask(): void {
        Auth::requireRole(['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG']);

        $input = json_decode($this->getRawInput(), true);
        if (!is_array($input) || json_last_error() !== JSON_ERROR_NONE) {
            $this->json(['success' => false, 'error' => 'Requête JSON invalide'], 400);
        }

        $question = trim((string)($input['question'] ?? ''));
        $mode = trim((string)($input['mode'] ?? 'chat'));
        $agentConfig = require ROOT_PATH . '/config/agent.php';
        $allowedModes = array_keys($agentConfig['modes'] ?? ['chat' => 'Chat simple']);
        if (!in_array($mode, $allowedModes, true)) {
            $mode = 'chat';
        }

        if ($question === '') {
            $this->json(['success' => false, 'error' => 'Question vide'], 400);
        }

        if ($mode === 'chat' && $this->isGreetingQuestion($question)) {
            $this->json([
                'success' => true,
                'reponse' => 'Bonjour ! Je suis là pour vous aider.',
                'mode'    => $mode,
                'timestamp' => date('c'),
            ]);
            return;
        }

        if ($mode === 'chat' && $this->isGreetingQuestion($question)) {
            $this->json([
                'success' => true,
                'reponse' => 'Bonjour ! Je suis là pour vous aider.',
                'mode'    => $mode,
                'timestamp' => date('c'),
            ]);
            return;
        }

        try {
            // 1. Récupérer les données en temps réel
            $donnees = $this->collecteDataRealtime();

            // 2. Construire le prompt avec contexte
            $prompt = $this->construirPrompt($question, $mode, $donnees);

            // 3. Appeler l'IA
            $reponse = $this->appelClaude($prompt);

            // 4. Retourner en JSON
            $this->json([
                'success' => true,
                'reponse' => $reponse,
                'mode'    => $mode,
                'timestamp' => date('c'),
            ]);

        } catch (Throwable $e) {
            // Lors des tests, les stubs de `json()` peuvent lancer une exception
            // marquée 'Test response sent'. Dans ce cas, propager l'exception
            // pour laisser le test capter la première réponse JSON et éviter
            // d'écraser cette réponse avec l'erreur ici.
            if ($e instanceof RuntimeException && $e->getMessage() === 'Test response sent') {
                throw $e;
            }

            $this->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Collecte toutes les données réelles de la base selon le rôle
     */
    protected function collecteDataRealtime(): array {
        $user = Auth::user();
        $role = $user['role'] ?? 'AGENT';
        $agencyId = AgencyContext::resolveAgencyId();

        $data = [
            'user' => $user,
            'agency_id' => $agencyId,
            'entreprise' => $this->getEntrepriseProfil(),
            'soldes' => $this->getSoldes($agencyId),
            'alertes' => $this->getAlertes(),
            'transactions_jour' => $this->getTransactionsAujourdhui(),
            'historique_30j' => $this->getHistorique30j(),
            'analyses' => $this->detectAnomaliesEtTendances(),
        ];

        // Les commissions sont sensibles : filtrées par rôle
        if (in_array($role, ['COMPTABLE', 'DG'])) {
            $data['commissions_mois'] = $this->getCommissionsMois();
        } else {
            $data['commissions_mois'] = ['total_mois' => 0, 'par_service' => []];
        }

        return $data;
    }

    /**
     * Récupère l'état actuel des soldes (Float + Caisse)
     */
    private function isGreetingQuestion(string $question): bool {
        $clean = mb_strtolower(trim($question));
        $patterns = [
            '/\bbonjour\b/',
            '/\bsalut\b/',
            '/\bhello\b/',
            '/\bbonsoir\b/',
            '/\bcoucou\b/',
            '/\bhey\b/',
            '/\bsalutations\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $clean)) {
                return true;
            }
        }

        $assistantPatterns = [
            '/\bagent\b/',
            '/\bia\b/',
            '/\bassistant\b/',
            '/\bbot\b/',
        ];

        foreach ($assistantPatterns as $pattern) {
            if (preg_match($pattern, $clean) && preg_match('/\b(bonjour|salut|hello|bonsoir|coucou|hey|salutations)\b/', $clean)) {
                return true;
            }
        }

        return false;
    }

    private function getSoldes(?int $agencyId = null): array {
        $query = "
            SELECT 
                s.nom as service,
                ss.type_solde,
                ss.montant_actuel,
                sa.valeur_seuil AS montant_seuil
            FROM solde_service ss
            JOIN service s ON ss.id_service = s.id_service
            LEFT JOIN seuil_alerte sa ON ss.id_solde = sa.id_solde
            WHERE s.actif = 1
        ";

        $params = [];
        if ($agencyId !== null) {
            $query .= " AND ss.id_agence = ?";
            $params[] = $agencyId;
        }

        $query .= " ORDER BY s.nom, ss.type_solde";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultats = $stmt->fetchAll();

        $soldes = [];
        foreach ($resultats as $row) {
            $service = $row['service'];
            if (!isset($soldes[$service])) {
                $soldes[$service] = [];
            }
            $soldes[$service][$row['type_solde']] = [
                'montant' => (float)$row['montant_actuel'],
                'seuil'   => (float)($row['montant_seuil'] ?? 0),
                'alerte'  => ((float)$row['montant_actuel'] < (float)($row['montant_seuil'] ?? 0))
            ];
        }

        return $soldes;
    }

    /**
     * Récupère les alertes actives
     */
    private function getAlertes(): array {
        $agencyId = AgencyContext::resolveAgencyId();
        // Le schéma stocke le seuil dans `seuil_alerte` (lié à `solde_service`).
        // Jointure : alerte_solde -> seuil_alerte -> solde_service -> service
        $query = "
            SELECT
                a.id_alerte,
                s.nom AS service,
                ss.type_solde,
                a.montant_au_moment AS montant_actuel,
                sa.valeur_seuil AS montant_seuil,
                CASE WHEN a.montant_au_moment < sa.valeur_seuil THEN 'CRITIQUE' ELSE 'WARN' END AS severity,
                a.date_alerte,
                a.message
            FROM alerte_solde a
            JOIN seuil_alerte sa ON a.id_seuil = sa.id_seuil
            JOIN solde_service ss ON sa.id_solde = ss.id_solde
            JOIN service s ON ss.id_service = s.id_service
            WHERE a.statut = 'ACTIVE'
        ";

        $params = [];
        if ($agencyId !== null) {
            $query .= " AND ss.id_agence = ?";
            $params[] = $agencyId;
        }

        $query .= " ORDER BY a.date_alerte DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les transactions du jour
     */
    private function getTransactionsAujourdhui(): array {
        $agencyId = AgencyContext::resolveAgencyId();
        $query = "
            SELECT 
                COUNT(*) as nb_transactions,
                SUM(t.montant) as volume_total,
                s.nom as service,
                COUNT(*) as nb_par_service
            FROM transaction t
            JOIN service s ON t.id_service = s.id_service
            WHERE DATE(t.created_at) = CURDATE()
            AND t.statut = 'VALIDEE'
        ";

        $params = [];
        if ($agencyId !== null) {
            $query .= " AND t.id_agence = ?";
            $params[] = $agencyId;
        }

        $query .= "
            GROUP BY t.id_service, s.nom
            ORDER BY COUNT(*) DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll();

        $nb_total = 0;
        $volume_total = 0;
        foreach ($resultats as $row) {
            $nb_total += (int)$row['nb_transactions'];
            $volume_total += (float)$row['volume_total'];
        }

        return [
            'nb_transactions' => $nb_total,
            'volume_total'    => $volume_total,
            'par_service'     => $resultats
        ];
    }

    /**
     * Récupère les commissions du mois courant
     */
    private function getCommissionsMois(): array {
        $agencyId = AgencyContext::resolveAgencyId();
        $query = "
            SELECT 
                SUM(ct.montant_commission) as total_commissions,
                s.nom as service,
                SUM(ct.montant_commission) as commissions_par_service
            FROM commission_transaction ct
            JOIN transaction t ON ct.id_transaction = t.id_transaction
            JOIN service s ON t.id_service = s.id_service
            WHERE YEAR(t.created_at) = YEAR(CURDATE())
            AND MONTH(t.created_at) = MONTH(CURDATE())
            AND t.statut = 'VALIDEE'
        ";

        $params = [];
        if ($agencyId !== null) {
            $query .= " AND t.id_agence = ?";
            $params[] = $agencyId;
        }

        $query .= "
            GROUP BY t.id_service, s.nom
            ORDER BY SUM(ct.montant_commission) DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll();

        $total = 0;
        foreach ($resultats as $row) {
            $total += (float)$row['total_commissions'];
        }

        return [
            'total_mois' => $total,
            'par_service' => $resultats
        ];
    }

    /**
     * Profil de l'entreprise et liste des services / agences actives
     */
    private function getEntrepriseProfil(): array {
        return [
            'nom' => APP_NAME,
            'description' => 'BK Business est une agence de services financiers et de transfert d’argent basée à Yaoundé, Cameroun.',
            'base_url' => BASE_URL,
            'services' => $this->getActiveServices(),
            'agences' => $this->getActiveAgences(),
        ];
    }

    private function getActiveServices(): array {
        $query = "SELECT nom, description, categorie FROM service WHERE actif = 1 ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getActiveAgences(): array {
        $query = "SELECT nom, code, adresse, ville FROM agence WHERE actif = 1 ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    protected function getRawInput(): string {
        return file_get_contents('php://input');
    }

    /**
     * Récupère l'historique des 30 derniers jours
     */
    private function getHistorique30j(): array {
        $agencyId = AgencyContext::resolveAgencyId();
        $query = "
            SELECT 
                DATE(t.created_at) as date_jour,
                COUNT(*) as nb_transactions,
                SUM(t.montant) as volume_total
            FROM transaction t
            WHERE t.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            AND t.statut = 'VALIDEE'
        ";

        $params = [];
        if ($agencyId !== null) {
            $query .= " AND t.id_agence = ?";
            $params[] = $agencyId;
        }

        $query .= "
            GROUP BY DATE(t.created_at)
            ORDER BY DATE(t.created_at) DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function buildPredictionSummary(array $history): array {
        if (empty($history)) {
            return [
                'forecast_transactions' => 0,
                'forecast_volume' => 0,
                'trend' => 'stable',
            ];
        }

        $values = array_values(array_filter($history, fn($row) => is_array($row)));
        $resolveRowDate = function (array $row): ?string {
            foreach (['date_jour', 'jour', 'date', 'created_at'] as $key) {
                $value = $row[$key] ?? null;
                if (is_string($value) && $value !== '') {
                    return $value;
                }
            }

            return null;
        };
        if (empty($values)) {
            return [
                'forecast_transactions' => 0,
                'forecast_volume' => 0,
                'trend' => 'stable',
            ];
        }

        $recentWindow = array_slice($values, -14);
        $recentTransactions = array_map(fn($row) => (int)($row['nb_transactions'] ?? 0), $recentWindow);
        $recentVolume = array_map(fn($row) => (float)($row['volume_total'] ?? 0), $recentWindow);

        $avgTransactions = (float)array_sum($recentTransactions) / max(1, count($recentTransactions));
        $avgVolume = (float)array_sum($recentVolume) / max(1, count($recentVolume));

        $lastTransactions = (int)($recentTransactions[count($recentTransactions) - 1] ?? 0);
        $lastVolume = (float)($recentVolume[count($recentVolume) - 1] ?? 0);
        $previousTransactions = (int)($recentTransactions[count($recentTransactions) - 2] ?? $lastTransactions);
        $previousVolume = (float)($recentVolume[count($recentVolume) - 2] ?? $lastVolume);

        $trendTransactions = $previousTransactions > 0
            ? (($lastTransactions - $previousTransactions) / $previousTransactions) * 100
            : 0;
        $trendVolume = $previousVolume > 0
            ? (($lastVolume - $previousVolume) / $previousVolume) * 100
            : 0;

        $seasonalityBaselineTransactions = $avgTransactions;
        $seasonalityBaselineVolume = $avgVolume;

        $forecastDate = null;
        $lastDate = $resolveRowDate($values[count($values) - 1] ?? []);
        if (is_string($lastDate) && strtotime($lastDate) !== false) {
            $forecastDate = date('Y-m-d', strtotime($lastDate . ' +1 day'));
        }

        $weekdayBuckets = [];
        foreach ($values as $row) {
            $rowDate = $resolveRowDate($row);
            if (is_string($rowDate) && strtotime($rowDate) !== false) {
                $weekday = (int)date('w', strtotime($rowDate));
                $weekdayBuckets[$weekday][] = $row;
            }
        }

        if ($forecastDate !== null) {
            $forecastWeekday = (int)date('w', strtotime($forecastDate));
            $sameWeekdayValues = $weekdayBuckets[$forecastWeekday] ?? [];
            if (!empty($sameWeekdayValues)) {
                $weekdayAvgTransactions = (float)array_sum(array_map(fn($row) => (int)($row['nb_transactions'] ?? 0), $sameWeekdayValues)) / count($sameWeekdayValues);
                $weekdayAvgVolume = (float)array_sum(array_map(fn($row) => (float)($row['volume_total'] ?? 0), $sameWeekdayValues)) / count($sameWeekdayValues);

                $seasonalityBaselineTransactions = max(
                    $avgTransactions,
                    ($avgTransactions * 0.15) + ($weekdayAvgTransactions * 0.85)
                );
                $seasonalityBaselineVolume = max(
                    $avgVolume,
                    ($avgVolume * 0.15) + ($weekdayAvgVolume * 0.85)
                );
            }
        }

        $smoothedTransactions = max(0, (int) round($seasonalityBaselineTransactions));
        $smoothedVolume = max(0, (float) round($seasonalityBaselineVolume, 2));

        $trend = $trendTransactions >= 0 ? 'up' : 'down';
        $growthFactorTransactions = 0.0;
        $growthFactorVolume = 0.0;

        if ($lastTransactions > $avgTransactions * 1.5 || $lastVolume > $avgVolume * 1.5) {
            $trend = 'volatile';
            $growthFactorTransactions = 0.05;
            $growthFactorVolume = 0.05;
        } else {
            $growthFactorTransactions = max(-0.2, min(0.2, $trendTransactions / 100));
            $growthFactorVolume = max(-0.2, min(0.2, $trendVolume / 100));
        }

        $forecastTransactions = max(0, (int) round($smoothedTransactions * (1 + $growthFactorTransactions)));
        $forecastVolume = max(0, (float) round($smoothedVolume * (1 + $growthFactorVolume), 2));

        return [
            'forecast_transactions' => $forecastTransactions,
            'forecast_volume' => $forecastVolume,
            'trend' => $trend,
        ];
    }

    /**
     * Détecte anomalies et tendances transactionnelles
     */
    private function detectAnomaliesEtTendances(): array {
        $agentConfig = require ROOT_PATH . '/config/agent.php';
        $multiplier = $agentConfig['thresholds']['transaction_anomaly_multiplier'] ?? 3;

        $queryToday = "
            SELECT
                s.nom AS service,
                COUNT(*) AS nb_transactions,
                SUM(t.montant) AS volume_total,
                AVG(t.montant) AS moyenne_transaction
            FROM transaction t
            JOIN service s ON t.id_service = s.id_service
            WHERE DATE(t.created_at) = CURDATE()
              AND t.statut = 'VALIDEE'
            GROUP BY t.id_service, s.nom
        ";

        $stmt = $this->db->prepare($queryToday);
        $stmt->execute();
        $todayStats = $stmt->fetchAll();

        $queryBaseline = "
            SELECT
                s.nom AS service,
                AVG(daily.moyenne_transaction) AS moyenne_montant_30j,
                AVG(daily.nb_transactions) AS moyenne_nb_30j,
                SUM(daily.volume_jour) AS volume_30j
            FROM (
                SELECT
                    t.id_service,
                    DATE(t.created_at) AS jour,
                    COUNT(*) AS nb_transactions,
                    SUM(t.montant) AS volume_jour,
                    AVG(t.montant) AS moyenne_transaction
                FROM transaction t
                WHERE t.created_at >= DATE_SUB(CURDATE(), INTERVAL 31 DAY)
                  AND t.created_at < CURDATE()
                  AND t.statut = 'VALIDEE'
                GROUP BY t.id_service, DATE(t.created_at)
            ) daily
            JOIN service s ON daily.id_service = s.id_service
            GROUP BY daily.id_service, s.nom
        ";

        $stmt = $this->db->prepare($queryBaseline);
        $stmt->execute();
        $baselineRows = $stmt->fetchAll();

        $baseline = [];
        foreach ($baselineRows as $row) {
            $baseline[$row['service']] = [
                'moyenne_montant_30j' => (float)$row['moyenne_montant_30j'],
                'moyenne_nb_30j' => (float)$row['moyenne_nb_30j'],
                'volume_30j' => (float)$row['volume_30j'],
            ];
        }

        $anomalies = [];
        foreach ($todayStats as $stats) {
            $service = $stats['service'];
            $avgToday = (float)$stats['moyenne_transaction'];
            $nbToday = (int)$stats['nb_transactions'];
            $baselineService = $baseline[$service] ?? null;

            if ($baselineService) {
                $ratioMontant = $baselineService['moyenne_montant_30j'] > 0
                    ? $avgToday / $baselineService['moyenne_montant_30j']
                    : null;
                $ratioNb = $baselineService['moyenne_nb_30j'] > 0
                    ? $nbToday / $baselineService['moyenne_nb_30j']
                    : null;

                if ($ratioMontant !== null && $ratioMontant >= $multiplier) {
                    $anomalies[] = [
                        'service' => $service,
                        'type' => 'Montant élevé',
                        'message' => sprintf(
                            "%s est %sx supérieur à la moyenne des 30 derniers jours (moyenne: %s FCFA, aujourd'hui: %s FCFA)",
                            $service,
                            number_format($ratioMontant, 1, ',', ' '),
                            number_format($baselineService['moyenne_montant_30j'], 0, ',', ' '),
                            number_format($avgToday, 0, ',', ' ')
                        ),
                    ];
                }

                if ($ratioNb !== null && $ratioNb >= $multiplier) {
                    $anomalies[] = [
                        'service' => $service,
                        'type' => 'Volume élevé',
                        'message' => sprintf(
                            "%s a %sx plus de transactions qu'en moyenne (moyenne: %d, aujourd'hui: %d)",
                            $service,
                            number_format($ratioNb, 1, ',', ' '),
                            round($baselineService['moyenne_nb_30j']),
                            $nbToday
                        ),
                    ];
                }
            }
        }

        $queryTrend = "
            SELECT
                s.nom AS service,
                SUM(CASE WHEN t.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN t.montant ELSE 0 END) AS volume_7j,
                SUM(CASE WHEN DATE(t.created_at) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                    AND DATE(t.created_at) < DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN t.montant ELSE 0 END) AS volume_precedent_7j
            FROM transaction t
            JOIN service s ON t.id_service = s.id_service
            WHERE t.created_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
              AND t.statut = 'VALIDEE'
            GROUP BY s.nom
        ";

        $stmt = $this->db->prepare($queryTrend);
        $stmt->execute();
        $trendRows = $stmt->fetchAll();

        $tendances = [];
        foreach ($trendRows as $row) {
            $previous = (float)$row['volume_precedent_7j'];
            $current = (float)$row['volume_7j'];
            $pourcent = $previous > 0 ? (($current - $previous) / $previous) * 100 : null;
            $tendances[] = [
                'service' => $row['service'],
                'volume_7j' => $current,
                'volume_precedent_7j' => $previous,
                'variation_pourcent' => $pourcent,
            ];
        }

        usort($tendances, function($a, $b) {
            return ($b['variation_pourcent'] ?? 0) <=> ($a['variation_pourcent'] ?? 0);
        });

        return [
            'anomalies' => $anomalies,
            'tendances' => array_slice($tendances, 0, 3),
        ];
    }

    /**
     * Construit le prompt avec contexte
     */
    private function construirPrompt(string $question, string $mode, array $donnees): string {
        $user = $donnees['user'];
        $entreprise = $donnees['entreprise'];
        $soldes = $donnees['soldes'];
        $alertes = $donnees['alertes'];
        $tx_jour = $donnees['transactions_jour'];
        $comm_mois = $donnees['commissions_mois'];
        $analyses = $donnees['analyses'] ?? ['anomalies' => [], 'tendances' => []];

        unset($user['email']);

        // Formatter les soldes
        $soldes_text = "SOLDES ACTUELS :\n";
        foreach ($soldes as $service => $types) {
            $soldes_text .= "\n  $service :\n";
            foreach ($types as $type => $data) {
                $status = $data['alerte'] ? ' ⚠️ ALERTE' : ' ✅ OK';
                $soldes_text .= sprintf(
                    "    %s: %s FCFA (seuil: %s FCFA)%s\n",
                    $type,
                    number_format($data['montant'], 0, ',', ' '),
                    number_format($data['seuil'], 0, ',', ' '),
                    $status
                );
            }
        }

        // Formatter les alertes
        $alertes_text = empty($alertes)
            ? "Aucune alerte active."
            : "ALERTES ACTIVES (" . count($alertes) . ") :\n" .
              implode("\n", array_map(function($a) {
                  return sprintf(
                      "  - %s (%s): %s FCFA (seuil: %s FCFA)",
                      $a['service'], $a['type_solde'],
                      number_format($a['montant_actuel'], 0, ',', ' '),
                      number_format($a['montant_seuil'], 0, ',', ' ')
                  );
              }, $alertes));

        // Formatter les transactions du jour
        $tx_text = sprintf(
            "TRANSACTIONS AUJOURD'HUI :\n  Nombre: %d | Volume: %s FCFA",
            $tx_jour['nb_transactions'],
            number_format($tx_jour['volume_total'], 0, ',', ' ')
        );

        $predictionSummary = $this->buildPredictionSummary($donnees['historique_30j'] ?? []);
        $predictionText = sprintf(
            "PRÉVISION COURTE DURÉE : %d transactions et %.2f FCFA de volume estimés à partir de la tendance récente (%s).",
            $predictionSummary['forecast_transactions'],
            $predictionSummary['forecast_volume'],
            $predictionSummary['trend'] === 'up' ? 'hausse' : 'stabilité/réduction'
        );

        // Formatter les commissions
        $comm_text = '';
        if (!empty($comm_mois['total_mois']) || !empty($comm_mois['par_service'])) {
            $comm_text = sprintf(
                "COMMISSIONS CE MOIS :\n  Total: %s FCFA",
                number_format($comm_mois['total_mois'], 0, ',', ' ')
            );
        } else {
            $comm_text = "COMMISSIONS : Données non accessibles selon votre rôle.";
        }

        // Formatter les anomalies et tendances
        $anomalies_text = empty($analyses['anomalies'])
            ? "Aucune anomalie transactionnelle détectée."
            : "ANOMALIES DÉTECTÉES :\n" . implode("\n", array_map(function($a) {
                return sprintf("  - [%s] %s", $a['type'], $a['message']);
            }, $analyses['anomalies']));

        $tendances_text = empty($analyses['tendances'])
            ? "Aucune tendance notable ce mois."
            : "TENDANCES :\n" . implode("\n", array_map(function($t) {
                $variation = $t['variation_pourcent'] !== null
                    ? number_format($t['variation_pourcent'], 1, ',', ' ') . "%"
                    : 'N/A';
                return sprintf(
                    "  - %s : %s FCFA cette semaine vs %s FCFA la semaine précédente (%s)",
                    $t['service'],
                    number_format($t['volume_7j'], 0, ',', ' '),
                    number_format($t['volume_precedent_7j'], 0, ',', ' '),
                    $variation
                );
            }, $analyses['tendances']));

        // Construire le prompt principal
        $roleInfo = match($user['role']) {
            'AGENT' => 'Tu as accès aux données de base (transactions, soldes) mais NON aux commissions détaillées.',
            'SUPERVISEUR' => 'Tu as accès aux soldes, alertes et transactions.',
            'COMPTABLE' => 'Tu as accès complet aux données financières et commissions.',
            'DG' => 'Tu as accès COMPLET à toutes les données.',
            default => ''
        };

        if (!IA_ENABLED) {
            throw new RuntimeException('L’agent IA est désactivé.');
        }

        $services_list = empty($entreprise['services']) ? 'Aucun service actif trouvé.' : implode(', ', array_map(function($s) {
            return $s['nom'];
        }, $entreprise['services']));

        $agences_list = empty($entreprise['agences']) ? 'Aucune agence active trouvée.' : implode(', ', array_map(function($a) {
            return trim($a['nom'] . ($a['ville'] ? ' (' . $a['ville'] . ')' : ''));
        }, $entreprise['agences']));

        $entreprise_text = sprintf(
            "ENTREPRISE : %s\nDESCRIPTION : %s\nBASE_URL : %s\nSERVICES ACTIFS : %s\nAGENCES ACTIVES : %s\n",
            $entreprise['nom'], 
            $entreprise['description'],
            $entreprise['base_url'],
            $services_list,
            $agences_list
        );

        $modeInstructions = match($mode) {
            'chat' => 'Mode CHAT : Réponse claire, concise et accessible. Fournis un résumé utile, des points clés et éventuellement une recommandation d\'action, sans développer excessivement.',
            'analyse' => 'Mode ANALYSE : Détecte les patterns, anomalies et tendances. Format : ✅ Points positifs / ⚠️ Points d\'attention / 💡 Recommandations.',
            'rapport' => 'Mode RAPPORT : Génère un rapport structuré, professionnel et factuel.',
            'prediction' => 'Mode PRÉDICTION : Anticipe les besoins à court terme (24h-7j), en se basant sur les tendances actuelles.',
            'alerte' => 'Mode ALERTE : Donne une évaluation rapide des alertes actives et propose des actions correctives.',
                'guichet' => 'Mode GUICHET : Fournis des instructions courtes et actions immédiates pour l\'agent au guichet.',
            default => 'Mode CHAT'
        };

        // Texte guide pour le guichet (inclus seulement si mode=guichet)
        $guichet_text = '';
        if ($mode === 'guichet') {
            $guichet_text = "\n=== GUIDE GUICHET ===\n- Vérifier identité client et référence transaction.\n- Confirmer montant et motif.\n- Si suspicion (montant > 3x moyenne) : STOP, demande confirmation SMS + appeler superviseur.\n- Pour retrait valide : étapes de vérification et confirmation écrite courte.\n- Si commission ou solde insuffisant : proposer alternatives et transférer vers service compétent.\n";
        }

        $prompt = <<<PROMPT
Tu es BK Assistant, l'agent IA de BK_Business, une agence de services d'argent à Yaoundé, Cameroun.

IDENTITÉ :

RÔLE DE L'UTILISATEUR :
$roleInfo

MODE DE FONCTIONNEMENT ACTUEL :
$modeInstructions

=== OBJECTIF DE RÉPONSE ===
- Réponds de façon concise, claire et professionnelle.
- Donne un résumé utile, puis un ou deux points clés.
- Si la question est opérationnelle, ajoute une recommandation d'action simple.
- Utilise des listes courtes seulement si cela améliore la clarté.
- Si tu manques de données, mentionne-le et propose un prochain point à vérifier.

=== PROFIL ENTREPRISE ===
$entreprise_text

=== PROFIL UTILISATEUR ===
- Nom : {$user['nom']}
- Rôle : {$user['role']}

=== DONNÉES EN TEMPS RÉEL ===

$soldes_text

$alertes_text

$tx_text

$predictionText

$comm_text

$anomalies_text

$tendances_text

=== QUESTION DE L'UTILISATEUR ===
$question

=== CONSIGNES ===
1. Utilise UNIQUEMENT les données fournies ci-dessus.
2. Ne jamais inventer de chiffres.
3. Si une donnée manque : "Cette information n'est pas disponible".
4. Formate les nombres avec des espaces : 125 000 FCFA.
5. Respecte le mode de fonctionnement indiqué.
6. Réponse professionnelle mais accessible.

RÉPONDS MAINTENANT :
- Si la question est une salutation ou une demande générale, réponds en 1 phrase simple.
- Ne fournis pas de détails sur les alertes ou les soldes tant que l'utilisateur ne le demande pas explicitement.
- Ne fais aucun commentaire sur ta propre réponse.
- Ne mets aucune information entre parenthèses concernant la structure ou la nature de ta réponse.
- Si l'utilisateur demande des informations sur les alertes, donne un résumé concis, puis propose une action claire.
$guichet_text
PROMPT;

        return $prompt;
    }

    /**
     * Appelle l'API Claude (Anthropic)
     */
    protected function appelClaude(string $prompt): string {
        $agentConfig = require ROOT_PATH . '/config/agent.php';
        $provider = Config::get('AI_PROVIDER', $agentConfig['provider'] ?? 'anthropic');

        try {
            if ($provider === 'groq') {
                require_once APP_PATH . '/models/ApiGroq.php';
                $api = new ApiGroq();
            } else {
                require_once APP_PATH . '/models/ApiClaude.php';
                $api = new ApiClaude();
            }

            return $api->call($prompt);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'appel au fournisseur IA ("' . $provider . '"): ' . $e->getMessage());
        }
    }

    /**
     * Génère un rapport structuré et formaté
     */
    public function rapport(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);

        try {
            $type = $this->get('type', 'daily'); // daily, monthly
            $donnees = $this->collecteDataRealtime();

            $reponse = match($type) {
                'daily' => $this->genererRapportJournalier($donnees),
                'monthly' => $this->genererRapportMensuel($donnees),
                default => ['error' => 'Type de rapport inconnu'],
            };

            $this->json($reponse);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function genererRapportJournalier(array $donnees): array {
        $tx = $donnees['transactions_jour'];
        $soldes = $donnees['soldes'];
        $alertes = $donnees['alertes'];
        $commissions = $donnees['commissions_mois'];

        return [
            'type' => 'Rapport Journalier',
            'date' => date('d/m/Y'),
            'heure_generation' => date('H:i:s'),
            'resume' => [
                'transactions' => $tx['nb_transactions'],
                'volume' => $tx['volume_total'],
                'alertes_actives' => count($alertes),
                'commissions_mois' => $commissions['total_mois']
            ],
            'soldes' => $soldes,
            'alertes' => $alertes,
            'historique_30j' => $donnees['historique_30j']
        ];
    }

    private function genererRapportMensuel(array $donnees): array {
        $soldes = $donnees['soldes'];
        $alertes = $donnees['alertes'];
        $commissions = $donnees['commissions_mois'];
        $hist = $donnees['historique_30j'];

        $nb_total = 0;
        $volume_total = 0;
        foreach ($hist as $jour) {
            $nb_total += (int)$jour['nb_transactions'];
            $volume_total += (float)$jour['volume_total'];
        }

        return [
            'type' => 'Rapport Mensuel',
            'mois' => date('m/Y'),
            'date_generation' => date('d/m/Y H:i:s'),
            'resume' => [
                'transactions_30j' => $nb_total,
                'volume_30j' => $volume_total,
                'transactions_moyenne_jour' => round($nb_total / 30, 2),
                'volume_moyen_jour' => round($volume_total / 30, 2),
                'commissions_total' => $commissions['total_mois'],
                'alertes_totales' => count($alertes)
            ],
            'soldes_actuels' => $soldes,
            'commissions_par_service' => $commissions['par_service'],
            'historique_30j' => $hist
        ];
    }
}
?>