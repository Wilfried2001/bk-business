<?php
// ============================================================
//  app/models/LoginAttempt.php — Tentatives de connexion
// ============================================================

class LoginAttempt extends Model {
    protected string $table = 'login_attempts';
    protected string $primaryKey = 'id_login_attempt';

    public function record(string $email, string $ipAddress, ?string $userAgent, bool $successful, ?string $lockUntil = null, ?string $reason = null): int {
        return $this->create([
            'email' => mb_strtolower(trim($email), 'UTF-8'),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'successful' => $successful ? 1 : 0,
            'lock_until' => $lockUntil,
            'reason' => $reason,
        ]);
    }

    public function getActiveLock(string $email, string $ipAddress): ?array {
        return $this->queryOne(
            "SELECT lock_until FROM login_attempts WHERE email = ? AND ip_address = ? AND lock_until IS NOT NULL ORDER BY created_at DESC LIMIT 1",
            [mb_strtolower(trim($email), 'UTF-8'), $ipAddress]
        );
    }

    public function countRecentFailedAttempts(string $email, string $ipAddress, int $windowMinutes = 15): int {
        $result = $this->queryOne(
            "SELECT COUNT(*) AS failed_count FROM login_attempts WHERE email = ? AND ip_address = ? AND successful = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [mb_strtolower(trim($email), 'UTF-8'), $ipAddress, $windowMinutes]
        );
        return (int)($result['failed_count'] ?? 0);
    }

    public function clearAttempts(string $email, string $ipAddress): bool {
        return $this->execute(
            "DELETE FROM login_attempts WHERE email = ? AND ip_address = ?",
            [mb_strtolower(trim($email), 'UTF-8'), $ipAddress]
        );
    }
}