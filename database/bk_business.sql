-- ============================================================
--  BK_BUSINESS — Script de création de la base de données
--  SGBD    : MySQL / MariaDB
--  Version : 1.0
--  Date    : 2025
-- ============================================================

CREATE DATABASE IF NOT EXISTS bk_business
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bk_business;

-- ============================================================
-- 1. TABLE : utilisateur
-- ============================================================
CREATE TABLE utilisateur (
    id_user         BIGINT          NOT NULL AUTO_INCREMENT,
    nom             VARCHAR(100)    NOT NULL,
    email           VARCHAR(150)    NOT NULL UNIQUE,
    mot_de_passe    VARCHAR(255)    NOT NULL,          -- stocké hashé (password_hash)
    role            ENUM(
                        'AGENT',
                        'SUPERVISEUR',
                        'COMPTABLE',
                        'DG'
                    )               NOT NULL,
    actif           TINYINT(1)      NOT NULL DEFAULT 1,
    date_creation   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 2. TABLE : service
-- ============================================================
CREATE TABLE service (
    id_service      BIGINT          NOT NULL AUTO_INCREMENT,
    nom             VARCHAR(100)    NOT NULL,
    description     VARCHAR(255)    NULL,
    categorie       ENUM(
                        'MOBILE_MONEY',
                        'INTERNATIONAL',
                        'ANNEXE'
                    )               NOT NULL,
    actif           TINYINT(1)      NOT NULL DEFAULT 1,
    PRIMARY KEY (id_service)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 3. TABLE : type_operation
-- ============================================================
CREATE TABLE type_operation (
    id_type         BIGINT          NOT NULL AUTO_INCREMENT,
    libelle         VARCHAR(100)    NOT NULL,
    description     VARCHAR(255)    NULL,
    impact_float    TINYINT         NOT NULL DEFAULT 0,   -- -1 | 0 | +1
    impact_caisse   TINYINT         NOT NULL DEFAULT 0,   -- -1 | 0 | +1
    PRIMARY KEY (id_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 4. TABLE : transaction
-- ============================================================
CREATE TABLE transaction (
    id_transaction  BIGINT          NOT NULL AUTO_INCREMENT,
    id_service      BIGINT          NOT NULL,
    id_type         BIGINT          NOT NULL,
    id_user         BIGINT          NOT NULL,
    montant         DECIMAL(15,2)   NOT NULL,
    reference       VARCHAR(100)    NULL,
    date_heure      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut          ENUM(
                        'EN_COURS',
                        'VALIDEE',
                        'ANNULEE'
                    )               NOT NULL DEFAULT 'VALIDEE',
    note            TEXT            NULL,
    PRIMARY KEY (id_transaction),
    CONSTRAINT fk_tx_service    FOREIGN KEY (id_service) REFERENCES service(id_service),
    CONSTRAINT fk_tx_type       FOREIGN KEY (id_type)    REFERENCES type_operation(id_type),
    CONSTRAINT fk_tx_user       FOREIGN KEY (id_user)    REFERENCES utilisateur(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 5. TABLE : solde_service
--    Un service a exactement 2 soldes : FLOAT et CAISSE
-- ============================================================
CREATE TABLE solde_service (
    id_solde        BIGINT          NOT NULL AUTO_INCREMENT,
    id_service      BIGINT          NOT NULL,
    type_solde      ENUM(
                        'FLOAT',
                        'CAISSE'
                    )               NOT NULL,
    montant_actuel  DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    date_maj        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_solde),
    UNIQUE KEY uq_service_type (id_service, type_solde),   -- 1 seul float + 1 seule caisse par service
    CONSTRAINT fk_solde_service FOREIGN KEY (id_service)   REFERENCES service(id_service)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 6. TABLE : mouvement_solde
--    Journal immuable — on n'UPDATE jamais, on INSERT toujours
-- ============================================================
CREATE TABLE mouvement_solde (
    id_mouvement    BIGINT          NOT NULL AUTO_INCREMENT,
    id_transaction  BIGINT          NOT NULL,
    id_solde        BIGINT          NOT NULL,
    nature          ENUM(
                        'CREDIT',
                        'DEBIT'
                    )               NOT NULL,
    montant         DECIMAL(15,2)   NOT NULL,
    solde_avant     DECIMAL(15,2)   NOT NULL,
    solde_apres     DECIMAL(15,2)   NOT NULL,
    date_heure      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    motif           VARCHAR(255)    NULL,
    PRIMARY KEY (id_mouvement),
    CONSTRAINT fk_mvt_transaction FOREIGN KEY (id_transaction) REFERENCES transaction(id_transaction),
    CONSTRAINT fk_mvt_solde       FOREIGN KEY (id_solde)       REFERENCES solde_service(id_solde)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 7. TABLE : seuil_alerte
--    Chaque solde possède exactement 1 seuil
-- ============================================================
CREATE TABLE seuil_alerte (
    id_seuil        BIGINT          NOT NULL AUTO_INCREMENT,
    id_solde        BIGINT          NOT NULL UNIQUE,       -- 1 seuil par solde
    valeur_seuil    DECIMAL(15,2)   NOT NULL,
    actif           TINYINT(1)      NOT NULL DEFAULT 1,
    PRIMARY KEY (id_seuil),
    CONSTRAINT fk_seuil_solde FOREIGN KEY (id_solde) REFERENCES solde_service(id_solde)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 8. TABLE : seuil_alerte_historique
--    Historique des modifications de seuil pour audit et suivi
-- ============================================================
CREATE TABLE seuil_alerte_historique (
    id_historique   BIGINT          NOT NULL AUTO_INCREMENT,
    id_seuil        BIGINT          NOT NULL,
    id_user         BIGINT          NOT NULL,
    ancienne_valeur DECIMAL(15,2)   NOT NULL,
    nouvelle_valeur DECIMAL(15,2)   NOT NULL,
    date_modification DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_historique),
    CONSTRAINT fk_historique_seuil FOREIGN KEY (id_seuil) REFERENCES seuil_alerte(id_seuil),
    CONSTRAINT fk_historique_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 8. TABLE : alerte_solde
--    Générée automatiquement quand montant_actuel < valeur_seuil
-- ============================================================
CREATE TABLE alerte_solde (
    id_alerte       BIGINT          NOT NULL AUTO_INCREMENT,
    id_seuil        BIGINT          NOT NULL,
    message         VARCHAR(255)    NOT NULL,
    montant_au_moment DECIMAL(15,2) NOT NULL,             -- solde au moment de l'alerte
    date_alerte     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut          ENUM(
                        'ACTIVE',
                        'TRAITEE'
                    )               NOT NULL DEFAULT 'ACTIVE',
    traite_par      BIGINT          NULL,                  -- id_user qui a traité
    date_traitement DATETIME        NULL,
    PRIMARY KEY (id_alerte),
    CONSTRAINT fk_alerte_seuil FOREIGN KEY (id_seuil)     REFERENCES seuil_alerte(id_seuil),
    CONSTRAINT fk_alerte_user  FOREIGN KEY (traite_par)   REFERENCES utilisateur(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 9. TABLE : commission_config
--    Grille tarifaire par service + type d'opération
-- ============================================================
CREATE TABLE commission_config (
    id_config       BIGINT          NOT NULL AUTO_INCREMENT,
    id_service      BIGINT          NOT NULL,
    id_type         BIGINT          NOT NULL,
    nom             VARCHAR(150)    NOT NULL,
    source          ENUM(
                        'OPERATEUR',
                        'CLIENT'
                    )               NOT NULL DEFAULT 'OPERATEUR',
    mode_calcul     ENUM(
                        'TAUX',
                        'FIXE',
                        'TRANCHE'
                    )               NOT NULL DEFAULT 'TAUX',
    valeur          DECIMAL(10,4)   NOT NULL DEFAULT 0,    -- taux % ou montant fixe
    actif           TINYINT(1)      NOT NULL DEFAULT 1,
    date_creation   DATE            NOT NULL DEFAULT (CURRENT_DATE),
    PRIMARY KEY (id_config),
    UNIQUE KEY uq_config (id_service, id_type, source),   -- 1 config par combinaison
    CONSTRAINT fk_config_service FOREIGN KEY (id_service) REFERENCES service(id_service),
    CONSTRAINT fk_config_type    FOREIGN KEY (id_type)    REFERENCES type_operation(id_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 10. TABLE : commission_tranche
--     Utilisée uniquement si mode_calcul = 'TRANCHE'
-- ============================================================
CREATE TABLE commission_tranche (
    id_tranche      BIGINT          NOT NULL AUTO_INCREMENT,
    id_config       BIGINT          NOT NULL,
    montant_min     DECIMAL(15,2)   NOT NULL,
    montant_max     DECIMAL(15,2)   NULL,                  -- NULL = sans plafond
    montant_fixe    DECIMAL(15,2)   NOT NULL,              -- commission fixe pour cette tranche
    PRIMARY KEY (id_tranche),
    CONSTRAINT fk_tranche_config FOREIGN KEY (id_config)  REFERENCES commission_config(id_config)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 11. TABLE : commission_transaction
--     Enregistre la commission calculée pour chaque transaction
-- ============================================================
CREATE TABLE commission_transaction (
    id_commission       BIGINT          NOT NULL AUTO_INCREMENT,
    id_transaction      BIGINT          NOT NULL,
    id_config           BIGINT          NOT NULL,
    source              ENUM(
                            'OPERATEUR',
                            'CLIENT'
                        )               NOT NULL,
    montant_commission  DECIMAL(15,2)   NOT NULL,
    est_benefice        TINYINT(1)      NOT NULL DEFAULT 1,
    date_calcul         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_commission),
    CONSTRAINT fk_comm_transaction FOREIGN KEY (id_transaction) REFERENCES transaction(id_transaction),
    CONSTRAINT fk_comm_config      FOREIGN KEY (id_config)      REFERENCES commission_config(id_config)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 12. VUE : benefice_service
--     Calcul dynamique — pas de table physique
-- ============================================================
CREATE OR REPLACE VIEW benefice_service AS
SELECT
    s.id_service,
    s.nom                               AS nom_service,
    s.categorie,
    COUNT(ct.id_commission)             AS nb_transactions,
    SUM(ct.montant_commission)          AS total_commission,
    SUM(CASE WHEN ct.est_benefice = 1
             THEN ct.montant_commission
             ELSE 0 END)                AS total_benefice,
    SUM(CASE WHEN ct.est_benefice = 0
             THEN ct.montant_commission
             ELSE 0 END)                AS total_perte,
    MONTH(t.date_heure)                 AS mois,
    YEAR(t.date_heure)                  AS annee
FROM commission_transaction ct
JOIN transaction t      ON ct.id_transaction = t.id_transaction
JOIN service s          ON t.id_service      = s.id_service
GROUP BY
    s.id_service,
    s.nom,
    s.categorie,
    YEAR(t.date_heure),
    MONTH(t.date_heure);


-- ============================================================
-- DONNÉES DE RÉFÉRENCE
-- ============================================================

-- Utilisateur admin par défaut (mot de passe : Admin@2025)
INSERT INTO utilisateur (nom, email, mot_de_passe, role) VALUES
('Directeur General',  'dg@bkbusiness.cm',          '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DG'),
('Superviseur',        'superviseur@bkbusiness.cm',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SUPERVISEUR'),
('Comptable',          'comptable@bkbusiness.cm',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COMPTABLE'),
('Agent Principal',    'agent@bkbusiness.cm',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'AGENT');

-- Services
INSERT INTO service (nom, description, categorie) VALUES
('Orange Money',   'Depots et retraits Orange Money',    'MOBILE_MONEY'),
('MTN Money',      'Depots et retraits MTN Money',       'MOBILE_MONEY'),
('Ria',            'Transferts internationaux Ria',       'INTERNATIONAL'),
('MoneyGram',      'Transferts internationaux MoneyGram', 'INTERNATIONAL'),
('Western Union',  'Transferts internationaux WU',        'INTERNATIONAL'),
('CashExpress',    'Transferts CashExpress',              'INTERNATIONAL'),
('SMobil',         'Services SMobil',                     'ANNEXE'),
('Scolarites',     'Paiement des frais de scolarite',     'ANNEXE'),
('Canal+',         'Reabonnement Canal+',                 'ANNEXE'),
('ENEO',           'Paiement factures ENEO',              'ANNEXE'),
('DHL',            'Envoi et reception colis DHL',        'ANNEXE');

-- Types d'opération avec impacts sur les soldes
INSERT INTO type_operation (libelle, description, impact_float, impact_caisse) VALUES
('Depot',           'Le client depose de l argent',          -1, +1),  -- Float diminue, Caisse augmente
('Retrait',         'Le client retire de l argent',          +1, -1),  -- Float augmente, Caisse diminue
('Envoi',           'Le client envoie de l argent',           0, +1),  -- Caisse augmente
('Reception',       'Le client reçoit de l argent',           0, -1),  -- Caisse diminue
('Paiement',        'Paiement d une facture ou service',     -1, +1),  -- Float diminue, Caisse augmente
('Reabonnement',    'Renouvellement d un abonnement',        -1, +1);  -- Float diminue, Caisse augmente

-- Soldes initiaux : 2 soldes par service (FLOAT + CAISSE) = 22 lignes
INSERT INTO solde_service (id_service, type_solde, montant_actuel) VALUES
(1,  'FLOAT',  0.00), (1,  'CAISSE', 0.00),  -- Orange Money
(2,  'FLOAT',  0.00), (2,  'CAISSE', 0.00),  -- MTN Money
(3,  'FLOAT',  0.00), (3,  'CAISSE', 0.00),  -- Ria
(4,  'FLOAT',  0.00), (4,  'CAISSE', 0.00),  -- MoneyGram
(5,  'FLOAT',  0.00), (5,  'CAISSE', 0.00),  -- Western Union
(6,  'FLOAT',  0.00), (6,  'CAISSE', 0.00),  -- CashExpress
(7,  'FLOAT',  0.00), (7,  'CAISSE', 0.00),  -- SMobil
(8,  'FLOAT',  0.00), (8,  'CAISSE', 0.00),  -- Scolarités
(9,  'FLOAT',  0.00), (9,  'CAISSE', 0.00),  -- Canal+
(10, 'FLOAT',  0.00), (10, 'CAISSE', 0.00),  -- ENEO
(11, 'FLOAT',  0.00), (11, 'CAISSE', 0.00);  -- DHL

-- Seuils d'alerte par défaut (à ajuster selon les besoins)
INSERT INTO seuil_alerte (id_solde, valeur_seuil) VALUES
(1,  20000.00),  -- Orange Money FLOAT
(2,  50000.00),  -- Orange Money CAISSE
(3,  15000.00),  -- MTN Money FLOAT
(4,  50000.00),  -- MTN Money CAISSE
(5,  50000.00),  -- Ria FLOAT
(6,  100000.00), -- Ria CAISSE
(7,  50000.00),  -- MoneyGram FLOAT
(8,  100000.00), -- MoneyGram CAISSE
(9,  50000.00),  -- Western Union FLOAT
(10, 100000.00), -- Western Union CAISSE
(11, 50000.00),  -- CashExpress FLOAT
(12, 100000.00), -- CashExpress CAISSE
(13, 10000.00),  -- SMobil FLOAT
(14, 30000.00),  -- SMobil CAISSE
(15, 10000.00),  -- Scolarités FLOAT
(16, 30000.00),  -- Scolarités CAISSE
(17, 10000.00),  -- Canal+ FLOAT
(18, 30000.00),  -- Canal+ CAISSE
(19, 10000.00),  -- ENEO FLOAT
(20, 30000.00),  -- ENEO CAISSE
(21, 10000.00),  -- DHL FLOAT
(22, 30000.00);  -- DHL CAISSE

-- ============================================================
-- INDEX pour optimiser les requêtes fréquentes
-- ============================================================
CREATE INDEX idx_transaction_service   ON transaction(id_service);
CREATE INDEX idx_transaction_date      ON transaction(date_heure);
CREATE INDEX idx_transaction_user      ON transaction(id_user);
CREATE INDEX idx_mouvement_transaction ON mouvement_solde(id_transaction);
CREATE INDEX idx_mouvement_solde       ON mouvement_solde(id_solde);
CREATE INDEX idx_commission_transaction ON commission_transaction(id_transaction);
CREATE INDEX idx_alerte_statut         ON alerte_solde(statut);

-- ============================================================
-- FIN DU SCRIPT
-- ============================================================
