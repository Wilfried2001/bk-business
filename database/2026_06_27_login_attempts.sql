-- ============================================================
--  Login attempts table for brute force protection
-- ============================================================

CREATE TABLE IF NOT EXISTS login_attempts (
    id_login_attempt BIGINT NOT NULL AUTO_INCREMENT,
    email VARCHAR(150) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    successful TINYINT(1) NOT NULL DEFAULT 0,
    reason VARCHAR(255) NULL,
    lock_until DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_login_attempt),
    INDEX idx_login_attempts_email_ip (email, ip_address),
    INDEX idx_login_attempts_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
