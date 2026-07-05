<?php
require_once __DIR__ . '/../core/Config.php';
Config::loadEnv(__DIR__ . '/../.env');
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/core/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS agence_solde_service (
    id_solde        BIGINT          NOT NULL AUTO_INCREMENT,
    id_agence       BIGINT          NOT NULL,
    id_service      BIGINT          NOT NULL,
    type_solde      ENUM('FLOAT','CAISSE') NOT NULL,
    montant_actuel  DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    date_maj        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_solde),
    UNIQUE KEY uq_agence_service_type (id_agence, id_service, type_solde),
    CONSTRAINT fk_agence_solde_agence  FOREIGN KEY (id_agence)  REFERENCES agence(id_agence),
    CONSTRAINT fk_agence_solde_service FOREIGN KEY (id_service)  REFERENCES service(id_service)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transfert_float (
    id_transfert          BIGINT          NOT NULL AUTO_INCREMENT,
    id_agence_source      BIGINT          NOT NULL,
    id_agence_destination BIGINT          NOT NULL,
    id_service            BIGINT          NOT NULL,
    id_demande_par        BIGINT          NOT NULL,
    id_valide_par         BIGINT          NULL,
    montant               DECIMAL(15,2)   NOT NULL,
    motif                 VARCHAR(255)    NULL,
    commentaire           TEXT            NULL,
    statut                ENUM('EN_ATTENTE','VALIDEE','EXECUTEE','REFUSEE') NOT NULL DEFAULT 'EN_ATTENTE',
    created_at            DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_transfert),
    CONSTRAINT fk_transfert_source_agence      FOREIGN KEY (id_agence_source)      REFERENCES agence(id_agence),
    CONSTRAINT fk_transfert_destination_agence FOREIGN KEY (id_agence_destination) REFERENCES agence(id_agence),
    CONSTRAINT fk_transfert_service            FOREIGN KEY (id_service)            REFERENCES service(id_service),
    CONSTRAINT fk_transfert_demande_par        FOREIGN KEY (id_demande_par)        REFERENCES utilisateur(id_user),
    CONSTRAINT fk_transfert_valide_par         FOREIGN KEY (id_valide_par)         REFERENCES utilisateur(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mouvement_solde_agence (
    id_mouvement    BIGINT          NOT NULL AUTO_INCREMENT,
    id_transfert    BIGINT          NOT NULL,
    id_solde        BIGINT          NOT NULL,
    nature          ENUM('CREDIT','DEBIT') NOT NULL,
    montant         DECIMAL(15,2)   NOT NULL,
    solde_avant     DECIMAL(15,2)   NOT NULL,
    solde_apres     DECIMAL(15,2)   NOT NULL,
    date_heure      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    motif           VARCHAR(255)    NULL,
    PRIMARY KEY (id_mouvement),
    CONSTRAINT fk_mvt_transfert         FOREIGN KEY (id_transfert) REFERENCES transfert_float(id_transfert),
    CONSTRAINT fk_mvt_agence_solde      FOREIGN KEY (id_solde)     REFERENCES agence_solde_service(id_solde)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
    $db->exec($sql);
    echo "Migration exécutée avec succès.";
} catch (Exception $e) {
    echo 'Erreur de migration : ' . $e->getMessage();
}
