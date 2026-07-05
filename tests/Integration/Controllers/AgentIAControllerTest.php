<?php
/**
 * Tests d'intégration pour AgentIAController
 */

class AgentIAControllerTest extends TestCase {

    protected AgentIAController $controller;

    protected function setUp(): void {
        parent::setUp();
        require_once APP_PATH . '/controllers/AgentIAController.php';
        $this->controller = new AgentIAController();
    }

    public function testGetSoldesReturnsStructuredArray(): void {
        $method = new ReflectionMethod(AgentIAController::class, 'getSoldes');
        $method->setAccessible(true);

        $soldes = $method->invoke($this->controller);

        $this->assertIsArray($soldes);
        foreach ($soldes as $service => $types) {
            $this->assertIsArray($types);
            foreach ($types as $type => $data) {
                $this->assertArrayHasKey('montant', $data);
                $this->assertArrayHasKey('seuil', $data);
                $this->assertArrayHasKey('alerte', $data);
            }
        }
    }

    public function testGetAlertesReturnsArray(): void {
        $method = new ReflectionMethod(AgentIAController::class, 'getAlertes');
        $method->setAccessible(true);

        $alertes = $method->invoke($this->controller);

        $this->assertIsArray($alertes);
        foreach ($alertes as $alerte) {
            $this->assertArrayHasKey('service', $alerte);
            $this->assertArrayHasKey('type_solde', $alerte);
            $this->assertArrayHasKey('message', $alerte);
        }
    }

    public function testConstruirPromptIncludesQuestionAndServiceSummary(): void {
        $method = new ReflectionMethod(AgentIAController::class, 'construirPrompt');
        $method->setAccessible(true);

        $donnees = [
            'user' => ['id' => 1, 'nom' => 'Agent Test', 'role' => 'AGENT'],
            'entreprise' => ['nom' => 'BK Business', 'description' => 'Test', 'base_url' => 'http://localhost', 'services' => [], 'agences' => []],
            'soldes' => [
                'Service A' => [
                    'FLOAT' => ['montant' => 50000, 'seuil' => 100000, 'alerte' => false],
                    'CAISSE' => ['montant' => 20000, 'seuil' => 15000, 'alerte' => false],
                ],
            ],
            'alertes' => [],
            'transactions_jour' => ['nb_transactions' => 1, 'volume_total' => 50000, 'par_service' => []],
            'commissions_mois' => ['total_mois' => 0, 'par_service' => []],
            'analyses' => ['anomalies' => [], 'tendances' => []],
        ];

        $prompt = $method->invoke($this->controller, 'Quel est le solde ?', 'chat', $donnees);

        $this->assertIsString($prompt);
        $this->assertStringContainsString('Quel est le solde ?', $prompt);
        $this->assertStringContainsString('SOLDES ACTUELS', $prompt);
        $this->assertStringContainsString('Service A', $prompt);
    }

    public function testDetectAnomaliesEtTendancesReturnsArrays(): void {
        $method = new ReflectionMethod(AgentIAController::class, 'detectAnomaliesEtTendances');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('anomalies', $result);
        $this->assertArrayHasKey('tendances', $result);
        $this->assertIsArray($result['anomalies']);
        $this->assertIsArray($result['tendances']);
    }

    public function testAskReturnsErrorForInvalidJson(): void {
        Auth::login(['id_user' => 1, 'nom' => 'Test Agent', 'role' => 'AGENT']);
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $controller = new class extends AgentIAController {
            public array $jsonResponse = [];
            public int $jsonStatus = 0;

            protected function getRawInput(): string {
                return '{invalid_json';
            }

            protected function json(array $data, int $status = 200): void {
                $this->jsonResponse = $data;
                $this->jsonStatus = $status;
                throw new RuntimeException('Test response sent');
            }
        };

        try {
            $controller->ask();
            $this->fail('Expected RuntimeException when JSON response is sent.');
        } catch (RuntimeException $exception) {
            $this->assertFalse($controller->jsonResponse['success']);
            $this->assertSame(400, $controller->jsonStatus);
            $this->assertSame('Requête JSON invalide', $controller->jsonResponse['error']);
        }
    }

    public function testAskReturnsErrorForEmptyQuestion(): void {
        Auth::login(['id_user' => 1, 'nom' => 'Test Agent', 'role' => 'AGENT']);
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $controller = new class extends AgentIAController {
            public array $jsonResponse = [];
            public int $jsonStatus = 0;

            protected function getRawInput(): string {
                return json_encode(['question' => '']);
            }

            protected function json(array $data, int $status = 200): void {
                $this->jsonResponse = $data;
                $this->jsonStatus = $status;
                throw new RuntimeException('Test response sent');
            }
        };

        try {
            $controller->ask();
            $this->fail('Expected RuntimeException when JSON response is sent.');
        } catch (RuntimeException $exception) {
            $this->assertFalse($controller->jsonResponse['success']);
            $this->assertSame(400, $controller->jsonStatus);
            $this->assertSame('Question vide', $controller->jsonResponse['error']);
        }
    }

    public function testAskReturnsSuccessForValidQuestion(): void {
        Auth::login(['id_user' => 1, 'nom' => 'Test Agent', 'role' => 'AGENT']);
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $controller = new class extends AgentIAController {
            public array $jsonResponse = [];
            public int $jsonStatus = 0;

            protected function getRawInput(): string {
                return json_encode(['question' => 'Quel est l état de mes soldes ?', 'mode' => 'chat']);
            }

            protected function collecteDataRealtime(): array {
                return [
                    'user' => ['id' => 1, 'nom' => 'Test Agent', 'role' => 'AGENT'],
                    'entreprise' => ['nom' => 'BK Business', 'description' => 'Test', 'base_url' => 'http://localhost', 'services' => [], 'agences' => []],
                    'soldes' => [],
                    'alertes' => [],
                    'transactions_jour' => ['nb_transactions' => 0, 'volume_total' => 0, 'par_service' => []],
                    'historique_30j' => [],
                    'analyses' => ['anomalies' => [], 'tendances' => []],
                    'commissions_mois' => ['total_mois' => 0, 'par_service' => []],
                ];
            }

            protected function appelClaude(string $prompt): string {
                return 'Réponse simulée';
            }

            protected function json(array $data, int $status = 200): void {
                $this->jsonResponse = $data;
                $this->jsonStatus = $status;
                throw new RuntimeException('Test response sent');
            }
        };

        try {
            $controller->ask();
            $this->fail('Expected RuntimeException when JSON response is sent.');
        } catch (RuntimeException $exception) {
            $this->assertArrayHasKey('reponse', $controller->jsonResponse);
            $this->assertSame('Réponse simulée', $controller->jsonResponse['reponse']);
            $this->assertSame(200, $controller->jsonStatus);
            $this->assertSame('chat', $controller->jsonResponse['mode'] ?? 'chat');
            $this->assertArrayHasKey('timestamp', $controller->jsonResponse);
        }
    }
}
