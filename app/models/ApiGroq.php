<?php
// ============================================================
//  app/models/ApiGroq.php — Classe pour appeler Groq (adaptateur)
// ============================================================

class ApiGroq {

    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private int $maxTokens;
    private int $timeout;

    public function __construct() {
        $config = require ROOT_PATH . '/config/agent.php';

        $this->apiKey = trim((string) Config::get('GROQ_API_KEY', $config['groq_api_key'] ?? ''));
        $this->apiUrl = trim((string) Config::get('GROQ_API_URL', $config['groq_api_url'] ?? ''));
        $this->model = $config['groq_model'] ?? 'groq-1';
        $this->maxTokens = (int)($config['max_tokens'] ?? 1024);
        $this->timeout = (int)($config['request_timeout'] ?? 15);

        if (empty($this->apiKey)) {
            throw new RuntimeException('Clé API Groq (GROQ_API_KEY) non configurée');
        }

        if (empty($this->apiUrl) || !filter_var($this->apiUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('URL API Groq (GROQ_API_URL) non configurée ou invalide: ' . $this->apiUrl);
        }
    }

    public function call(string $prompt, ?array $options = null): string {
        $options = $options ?? [];

        $payload = [
            'model' => $options['model'] ?? $this->model,
             'messages' => [
        [
            'role' => 'user',
            'content' => $prompt
        ]
    ],
            'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
        ];

        return $this->makeRequest($payload);
    }

    private function makeRequest(array $payload): string {
        if (empty($this->apiUrl) || !filter_var($this->apiUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('URL API Groq invalide ou manquante: ' . $this->apiUrl);
        }

        $ch = curl_init($this->apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $curlErrno !== 0) {
            throw new RuntimeException('Erreur curl Groq: ' . $curlError);
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Réponse API Groq invalide : ' . json_last_error_msg());
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMsg = 'Erreur inconnue';
            if (isset($data['error']['message'])) {
                $errorMsg = $data['error']['message'];
            } elseif (isset($data['message'])) {
                $errorMsg = $data['message'];
            }
            throw new RuntimeException('Erreur API Groq (HTTP ' . $httpCode . '): ' . $errorMsg);
        }

        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }

        if (isset($data['output']) && is_array($data['output'])) {
            if (isset($data['output'][0]['text'])) {
                return $data['output'][0]['text'];
            }
            if (isset($data['output'][0]['content']) && is_array($data['output'][0]['content'])) {
                foreach ($data['output'][0]['content'] as $contentItem) {
                    if (is_string($contentItem)) {
                        return $contentItem;
                    }
                    if (is_array($contentItem) && isset($contentItem['text'])) {
                        return $contentItem['text'];
                    }
                }
            }
        }

        if (isset($data['text']) && is_string($data['text'])) {
            return $data['text'];
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

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