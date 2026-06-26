-- ============================================================
--  Agent IA call logs for audit and troubleshooting
-- ============================================================

CREATE TABLE IF NOT EXISTS agent_ia_logs (
    id_agent_ia_log BIGINT NOT NULL AUTO_INCREMENT,
    id_user BIGINT NOT NULL,
    mode VARCHAR(50) NOT NULL,
    prompt TEXT NOT NULL,
    response TEXT NULL,
    success TINYINT(1) NOT NULL DEFAULT 0,
    error_message VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_agent_ia_log),
    INDEX idx_agent_ia_logs_user (id_user),
    INDEX idx_agent_ia_logs_mode (mode),
    CONSTRAINT fk_agent_ia_logs_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
