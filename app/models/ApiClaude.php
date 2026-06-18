<?php
// ============================================================
//  app/models/ApiClaude.php — Classe pour appeler Claude
// ============================================================

class ApiClaude {

    private string $apiKey;
    private string $model;
    private int $maxTokens;
    private int $timeout;

    public function __construct() {
        $config = require ROOT_PATH . '/config/agent.php';
        
        $this->apiKey = Config::get('ANTHROPIC_API_KEY', $config['api_key']);
        $this->model = $config['model'];
        $this->maxTokens = (int)($config['max_tokens'] ?? 1024);
        $this->timeout = (int)($config['request_timeout'] ?? 15);

        if (empty($this->apiKey)) {
            throw new RuntimeException('Clé API Anthropic non configurée');
        }
    }

    /**
     * Appelle l'API Claude et retourne la réponse
     */
    public function call(string $prompt, ?array $options = null): string {
        $options = $options ?? [];

        $payload = [
            'model' => $options['model'] ?? $this->model,
            'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ];

        // Si you avez system prompt
        if (!empty($options['system'])) {
            $payload['system'] = $options['system'];
        }

        return $this->makeRequest($payload);
    }

    /**
     * Effectue la requête HTTP vers l'API Anthropic
     */
    private function makeRequest(array $payload): string {
        $ch = curl_init('https://api.anthropic.com/v1/messages');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $this->apiKey,
            'anthropic-version: 2023-06-01',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $curlErrno !== 0) {
            throw new RuntimeException('Erreur curl: ' . $curlError);
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Réponse API Claude invalide : ' . json_last_error_msg());
        }

        if ($httpCode !== 200) {
            $errorMsg = 'Erreur inconnue';
            if (isset($data['error']['message'])) {
                $errorMsg = $data['error']['message'];
            } elseif (isset($data['message'])) {
                $errorMsg = $data['message'];
            }
            throw new RuntimeException('Erreur API Claude (HTTP ' . $httpCode . '): ' . $errorMsg);
        }

        if (isset($data['completion']) && is_string($data['completion'])) {
            return $data['completion'];
        }

        if (isset($data['message']['content']) && is_string($data['message']['content'])) {
            return $data['message']['content'];
        }

        if (isset($data['content'])) {
            if (is_array($data['content']) && isset($data['content'][0]['text'])) {
                return $data['content'][0]['text'];
            }
            if (is_string($data['content'])) {
                return $data['content'];
            }
        }

        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }

        if (isset($data['text']) && is_string($data['text'])) {
            return $data['text'];
        }

        throw new RuntimeException('Format de réponse Anthropic non reconnu. Contactez l\'administrateur.');
    }

    /**
     * Test de la connexion API
     */
    public static function testConnection(): bool {
        try {
            $api = new self();
            $response = $api->call('Réponds simplement: OK');
            return strpos($response, 'OK') !== false;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>