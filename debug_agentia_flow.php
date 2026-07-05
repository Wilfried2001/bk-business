<?php
require "tests/bootstrap.php";
require "app/controllers/AgentIAController.php";

Auth::login(['id_user' => 1, 'nom' => 'Test Agent', 'role' => 'AGENT']);
$_SERVER['HTTP_ACCEPT'] = 'application/json';

class X extends AgentIAController {
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
        echo "JSON CALLED status={$status} data=" . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
        throw new RuntimeException('Test response sent');
    }
}

try {
    $c = new X();
    $c->ask();
    echo "ASK returned normally\n";
} catch (Throwable $e) {
    echo get_class($e) . ": " . $e->getMessage() . "\n";
    echo "status=" . ($c->jsonStatus ?? 'N/A') . "\n";
    echo json_encode($c->jsonResponse, JSON_UNESCAPED_UNICODE) . "\n";
}
