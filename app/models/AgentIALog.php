<?php
// ============================================================
//  app/models/AgentIALog.php — Journalisation des appels IA
// ============================================================

class AgentIALog extends Model {
    protected string $table = 'agent_ia_logs';
    protected string $primaryKey = 'id_agent_ia_log';

    public function record(string $userId, string $mode, string $prompt, string $response, bool $success, ?string $errorMessage = null): int {
        return $this->create([
            'id_user' => $userId,
            'mode' => $mode,
            'prompt' => $prompt,
            'response' => $response,
            'success' => $success ? 1 : 0,
            'error_message' => $errorMessage,
        ]);
    }
}
