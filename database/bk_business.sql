-- ============================================================
--  BK_BUSINESS — Script de création de la base de données
--  SGBD    : MySQL / MariaDB
--  Version : 1.0
--  Date    : 2025
--  Note    : Ce script consolide les créations de tables et
--            les migrations complémentaires du projet.
-- ============================================================

DROP DATABASE IF EXISTS bk_business;
CREATE DATABASE bk_business
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
-- 2. TABLE : login_attempts
-- ============================================================
CREATE TABLE login_attempts (
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

-- ============================================================
-- 3. TABLE : agent_ia_logs
--    Journal d'audit des appels IA
-- ============================================================
CREATE TABLE agent_ia_logs (
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

-- ============================================================
-- 4. TABLE : presence_employe
-- ============================================================
CREATE TABLE presence_employe (
    id_presence BIGINT NOT NULL AUTO_INCREMENT,
    id_user BIGINT NOT NULL,
    date_presence DATE NOT NULL,
    statut ENUM('PRESENT','RETARD','ABSENT') NOT NULL DEFAULT 'PRESENT',
    motif_retard VARCHAR(255) NULL,
    commentaire VARCHAR(255) NULL,
    heure_arrivee TIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_presence),
    UNIQUE KEY uk_presence_user_date (id_user, date_presence),
    INDEX idx_presence_date (date_presence),
    CONSTRAINT fk_presence_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 5. TABLE : service
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
-- Seed des types d'operations par defaut
-- ============================================================
INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Envoi client', 'Envoi international client', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Envoi client');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Retrait client', 'Paiement ou retrait international client', 0, -1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Retrait client');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Retour de fond', 'Retour de fonds ou annulation payee', 0, -1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Retour de fond');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Annulation', 'Annulation operation internationale', 0, -1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Annulation');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Depot en especes', 'Depot especes en agence', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Depot en especes');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Cash in float', 'Approvisionnement float', 1, -1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Cash in float');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Cash out float', 'Sortie ou retrait float', -1, 1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Cash out float');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Envoi colis', 'Envoi de colis', -1, 1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Envoi colis');

-- ============================================================
-- 4. TABLE : agence
-- ============================================================
CREATE TABLE agence (
    id_agence      BIGINT          NOT NULL AUTO_INCREMENT,
    nom            VARCHAR(150)    NOT NULL,
    code           VARCHAR(50)     NULL,
    adresse        VARCHAR(255)    NULL,
    ville          VARCHAR(100)    NULL,
    actif          TINYINT(1)      NOT NULL DEFAULT 1,
    PRIMARY KEY (id_agence)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 5. TABLE : transaction
-- ============================================================
CREATE TABLE transaction (
    id_transaction      BIGINT          NOT NULL AUTO_INCREMENT,
    id_service          BIGINT          NOT NULL,
    id_type             BIGINT          NOT NULL,
    id_user             BIGINT          NOT NULL,
    agence              VARCHAR(150)    NULL,
    id_agence           BIGINT          NULL,
    reference           VARCHAR(100)    NULL,
    nom_expediteur      VARCHAR(255)    NULL,
    expediteur_identifiant VARCHAR(100) NULL,
    expediteur_telephone   VARCHAR(50)  NULL,
    nom_benefis         VARCHAR(255)    NULL,
    beneficiaire_identifiant VARCHAR(100) NULL,
    beneficiaire_telephone   VARCHAR(50)  NULL,
    code_operation      VARCHAR(100)    NULL,
    nature_operation    VARCHAR(100)    NULL,
    produit             VARCHAR(100)    NULL,
    type_de_operation   VARCHAR(100)    NULL,
    id_produit          BIGINT          NULL,
    montant             DECIMAL(15,2)   NOT NULL,
    created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    motif_transaction   VARCHAR(255)    NULL,
    nature_transaction  VARCHAR(100)    NULL,
    type_mouvement      VARCHAR(50)     NULL,
    affecte_stock       TINYINT(1)      NOT NULL DEFAULT 0,
    affecte_caisse      TINYINT(1)      NOT NULL DEFAULT 0,
    notes               TEXT            NULL,
    statut              ENUM(
                            'EN_COURS',
                            'VALIDEE',
                            'ANNULEE'
                        )               NOT NULL DEFAULT 'VALIDEE',
    updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    note                TEXT            NULL,
    PRIMARY KEY (id_transaction),
    CONSTRAINT fk_tx_service    FOREIGN KEY (id_service)  REFERENCES service(id_service),
    CONSTRAINT fk_tx_type       FOREIGN KEY (id_type)     REFERENCES type_operation(id_type),
    CONSTRAINT fk_tx_user       FOREIGN KEY (id_user)     REFERENCES utilisateur(id_user),
    CONSTRAINT fk_tx_agence     FOREIGN KEY (id_agence)   REFERENCES agence(id_agence)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 6. TABLE : solde_service
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
    MONTH(t.created_at)                 AS mois,
    YEAR(t.created_at)                  AS annee
FROM commission_transaction ct
JOIN transaction t      ON ct.id_transaction = t.id_transaction
JOIN service s          ON t.id_service      = s.id_service
GROUP BY
    s.id_service,
    s.nom,
    s.categorie,
    YEAR(t.created_at),
    MONTH(t.created_at);


-- ============================================================
-- 13. VUE : transaction_avec_commission
--     Remplace l'usage des anciennes colonnes transaction.commission
--     et transaction.frais_operation, supprimées car redondantes.
--     La commission affichée provient uniquement de commission_transaction,
--     qui reste la seule source de vérité.
-- ============================================================
CREATE OR REPLACE VIEW transaction_avec_commission AS
SELECT
    t.id_transaction,
    t.reference,
    t.id_service,
    s.nom                              AS nom_service,
    t.id_type,
    top.libelle                        AS libelle_type,
    t.montant,
    t.statut,
    t.created_at,
    COALESCE(SUM(ct.montant_commission), 0)                              AS commission_totale,
    COALESCE(SUM(CASE WHEN ct.source = 'OPERATEUR'
                       THEN ct.montant_commission ELSE 0 END), 0)        AS commission_operateur,
    COALESCE(SUM(CASE WHEN ct.source = 'CLIENT'
                       THEN ct.montant_commission ELSE 0 END), 0)        AS commission_client
FROM transaction t
JOIN service s                  ON s.id_service = t.id_service
JOIN type_operation top          ON top.id_type  = t.id_type
LEFT JOIN commission_transaction ct ON ct.id_transaction = t.id_transaction
GROUP BY
    t.id_transaction, t.reference, t.id_service, s.nom,
    t.id_type, top.libelle, t.montant, t.statut, t.created_at;


-- ============================================================
-- DONNÉES DE RÉFÉRENCE
-- ============================================================

-- Comptes de connexion pour chaque rôle (mot de passe: password)
INSERT INTO utilisateur (nom, email, mot_de_passe, role) VALUES
('Directeur General',  'dg@bkbusiness.cm',          '$2y$10$AcrfpWNGoq8VWJofkC1k/eJmuzwnx12cWgp9LgSys9MjoqhmioMfq', 'DG'),
('Superviseur',        'superviseur@bkbusiness.cm',  '$2y$10$AcrfpWNGoq8VWJofkC1k/eJmuzwnx12cWgp9LgSys9MjoqhmioMfq', 'SUPERVISEUR'),
('Comptable',          'comptable@bkbusiness.cm',    '$2y$10$AcrfpWNGoq8VWJofkC1k/eJmuzwnx12cWgp9LgSys9MjoqhmioMfq', 'COMPTABLE'),
('Agent Principal',    'agent@bkbusiness.cm',        '$2y$10$AcrfpWNGoq8VWJofkC1k/eJmuzwnx12cWgp9LgSys9MjoqhmioMfq', 'AGENT');

-- Comptes de test supplémentaires pour les 4 rôles (mot de passe: password)
INSERT INTO utilisateur (nom, email, mot_de_passe, role) VALUES
('Directeur Test',     'dg.test@bkbusiness.cm',      '$2y$10$AcrfpWNGoq8VWJofkC1k/eJmuzwnx12cWgp9LgSys9MjoqhmioMfq', 'DG'),
('Superviseur Test',   'superviseur.test@bkbusiness.cm', '$2y$10$AcrfpWNGoq8VWJofkC1k/eJmuzwnx12cWgp9LgSys9MjoqhmioMfq', 'SUPERVISEUR'),
('Comptable Test',     'comptable.test@bkbusiness.cm', '$2y$10$AcrfpWNGoq8VWJofkC1k/eJmuzwnx12cWgp9LgSys9MjoqhmioMfq', 'COMPTABLE'),
('Agent Test',         'agent.test@bkbusiness.cm',    '$2y$10$AcrfpWNGoq8VWJofkC1k/eJmuzwnx12cWgp9LgSys9MjoqhmioMfq', 'AGENT');

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
('Reabonnement',    'Renouvellement d un abonnement',        -1, +1),  -- Float diminue, Caisse augmente
('Envoi client',    'Envoi international client',             0, +1),  -- Caisse augmente
('Retrait client',  'Paiement ou retrait international client',0, -1), -- Caisse diminue
('Retour de fond',  'Retour de fonds ou annulation payee',    0, -1),  -- Caisse diminue
('Annulation',      'Annulation operation internationale',     0, -1),  -- Caisse diminue
('Depot en especes','Depot especes en agence',                0, +1),  -- Caisse augmente
('Cash in float',   'Approvisionnement float',               +1, -1),  -- Float augmente, caisse diminue
('Cash out float',  'Sortie ou retrait float',               -1, +1),  -- Float diminue, caisse augmente
('Envoi colis',     'Envoi de colis',                        -1, +1);  -- Float diminue, caisse augmente

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

-- Agences fictives
INSERT INTO agence (nom, code, adresse, ville) VALUES
('Agence Centre',    'CTR', 'Rue du Commerce n°12', 'Yaoundé'),
('Agence Marché',    'MKT', 'Avenue du Marché n°45', 'Douala'),
('Agence Airport',   'AIR', 'Terminal 2, Route de l''Aéroport', 'Douala'),
('Agence Downtown',  'DTN', 'Boulevard René II n°23', 'Yaoundé');

-- Configuration de commission fictive
INSERT INTO commission_config (id_service, id_type, nom, source, mode_calcul, valeur) VALUES
(1, 1, 'Commission Depot Orange Money', 'OPERATEUR', 'TAUX', 1.50),
(1, 2, 'Commission Retrait Orange Money', 'OPERATEUR', 'FIXE', 250.00),
(2, 5, 'Commission Paiement MTN Money', 'CLIENT', 'TRANCHE', 0.00),
(3, 3, 'Commission Envoi Ria', 'OPERATEUR', 'TAUX', 0.75),
(9, 6, 'Commission Reabonnement Canal+', 'CLIENT', 'FIXE', 500.00),
(11, 1, 'Commission Depot DHL', 'CLIENT', 'TAUX', 1.25);

-- Tranches de commission pour MTN Money Paiement
INSERT INTO commission_tranche (id_config, montant_min, montant_max, montant_fixe) VALUES
((SELECT id_config FROM commission_config WHERE id_service = 2 AND id_type = 5 AND source = 'CLIENT'), 0.00, 20000.00, 150.00),
((SELECT id_config FROM commission_config WHERE id_service = 2 AND id_type = 5 AND source = 'CLIENT'), 20000.01, 50000.00, 300.00),
((SELECT id_config FROM commission_config WHERE id_service = 2 AND id_type = 5 AND source = 'CLIENT'), 50000.01, NULL, 500.00);

-- Transactions fictives
-- Note : les colonnes "commission" et "frais_operation" ont été retirées.
-- La commission de chaque transaction est désormais exclusivement
-- enregistrée dans la table commission_transaction (voir plus bas),
-- qui reste l'unique source de vérité pour ces montants.
INSERT INTO transaction (id_service, id_type, id_user, agence, id_agence, reference, nom_expediteur, nom_benefis, code_operation, nature_operation, produit, type_de_operation, montant, motif_transaction, nature_transaction, type_mouvement, affecte_stock, affecte_caisse, notes, statut, note) VALUES
(1, 1, 4, 'Agence Centre', 1, 'OMD-2026-001', 'Franck Mbarga', 'Claire Ndongo', 'OMD001', 'Depot', 'Orange Money', 'Dépôt mobile', 120000.00, 'Dépôt espèces client', 'FINANCIER', 'ENTREE', 0, 1, 'Client a déposé de l''argent en agence', 'VALIDEE', 'Commission opérateur'),
(1, 2, 4, 'Agence Centre', 1, 'OMR-2026-002', 'Joseph Keng', 'Mireille Tchame', 'OMR002', 'Retrait', 'Orange Money', 'Retrait mobile', 35000.00, 'Retrait d''argent pour client', 'FINANCIER', 'SORTIE', 0, 1, 'Retrait réseau Orange', 'VALIDEE', 'Commission fixe'),
(2, 5, 3, 'Agence Marché', 2, 'MTN-PAY-003', 'Sandra Mba', 'Ecole Sainte Marie', 'MTNP003', 'Paiement', 'MTN Money', 'Paiement facture', 25000.00, 'Paiement facture scolaire', 'FINANCIER', 'ENTREE', 0, 1, 'Paiement élève', 'VALIDEE', 'Commission tranche'),
(3, 3, 4, 'Agence Marché', 2, 'RIA-ENVOI-004', 'Jean-Paul Etoundi', 'Antonio Silva', 'RIA004', 'Envoi', 'Ria', 'Transfert international', 180000.00, 'Envoi vers le Cameroun', 'FINANCIER', 'ENTREE', 0, 1, 'Transfert Ria vers Douala', 'VALIDEE', 'Commission opérateur'),
(3, 4, 4, 'Agence Marché', 2, 'RIA-RECP-005', 'Aicha Kotto', 'Samuel N.', 'RIA005', 'Reception', 'Ria', 'Reception international', 100000.00, 'Réception fonds international', 'FINANCIER', 'SORTIE', 0, 1, 'Reception de fonds', 'VALIDEE', 'Commission opérateur'),
(9, 6, 4, 'Agence Airport', 3, 'CNL-2026-006', 'Monique Fokam', 'Canal+ Service', 'CNL006', 'Reabonnement', 'Canal+', 'Abonnement TV', 7000.00, 'Reabonnement bouquet Canal+', 'FINANCIER', 'ENTREE', 0, 1, 'Reabonnement client', 'VALIDEE', 'Commission fixe'),
(10, 5, 3, 'Agence Airport', 3, 'ENEO-2026-007', 'Pierre Ngu', 'Société ENEO', 'ENEO007', 'Paiement', 'ENEO', 'Paiement facture', 20000.00, 'Paiement facture ENEO', 'FINANCIER', 'ENTREE', 0, 1, 'Paiement électricité', 'VALIDEE', 'Commission fixe'),
(11, 1, 2, 'Agence Downtown', 4, 'DHL-DEP-008', 'Emmanuel T.', 'Client DHL', 'DHL008', 'Depot', 'DHL', 'Dépôt colis', 35000.00, 'Dépôt frais DHL', 'FINANCIER', 'ENTREE', 0, 1, 'Dépôt pour envoi colis', 'VALIDEE', 'Commission client');


-- Mouvements de soldes liés aux transactions fictives
INSERT INTO mouvement_solde (id_transaction, id_solde, nature, montant, solde_avant, solde_apres, motif) VALUES
(1, 1, 'DEBIT', 120000.00, 150000.00, 30000.00, 'Retrait du float pour dépôt Orange Money'),
(1, 2, 'CREDIT', 120000.00, 20000.00, 140000.00, 'Entrée de caisse pour dépôt Orange Money'),
(2, 1, 'CREDIT', 35000.00, 30000.00, 65000.00, 'Augmentation du float suite au retrait'),
(2, 2, 'DEBIT', 35000.00, 140000.00, 105000.00, 'Sortie de caisse suite au retrait'),
(3, 3, 'DEBIT', 25000.00, 40000.00, 15000.00, 'Paiement MTN Money - sortie float'),
(3, 4, 'CREDIT', 25000.00, 30000.00, 55000.00, 'Paiement MTN Money - entrée caisse'),
(4, 6, 'CREDIT', 180000.00, 90000.00, 270000.00, 'Entrée caisse pour envoi Ria'),
(5, 6, 'DEBIT', 100000.00, 270000.00, 170000.00, 'Sortie caisse pour réception Ria'),
(6, 17, 'DEBIT', 7000.00, 15000.00, 8000.00, 'Sortie float pour reabonnement Canal+'),
(6, 18, 'CREDIT', 7000.00, 25000.00, 32000.00, 'Entrée caisse Canal+ pour reabonnement'),
(7, 19, 'DEBIT', 20000.00, 38000.00, 18000.00, 'Sortie float pour paiement ENEO'),
(7, 20, 'CREDIT', 20000.00, 30000.00, 50000.00, 'Entrée caisse ENEO paiement'),
(8, 21, 'DEBIT', 35000.00, 45000.00, 10000.00, 'Sortie float pour dépôt DHL'),
(8, 22, 'CREDIT', 35000.00, 10000.00, 45000.00, 'Entrée caisse dépôt DHL');

-- Commission de transactions fictives
INSERT INTO commission_transaction (id_transaction, id_config, source, montant_commission, est_benefice) VALUES
(1, 1, 'OPERATEUR', 1800.00, 1),
(2, 2, 'OPERATEUR', 250.00, 1),
(3, 3, 'CLIENT', 300.00, 0),
(4, 4, 'OPERATEUR', 1350.00, 1),
(5, 4, 'OPERATEUR', 750.00, 1),
(6, 5, 'CLIENT', 500.00, 0),
(7, 2, 'OPERATEUR', 400.00, 1),
(8, 6, 'CLIENT', 437.50, 0);

-- Alertes de solde fictives
INSERT INTO alerte_solde (id_seuil, message, montant_au_moment, statut) VALUES
(17, 'Solde FLOAT Canal+ inférieur au seuil de sécurité', 8000.00, 'ACTIVE');

-- Mise à jour des soldes actuels après les transactions fictives
UPDATE solde_service SET montant_actuel = 65000.00 WHERE id_service = 1 AND type_solde = 'FLOAT';
UPDATE solde_service SET montant_actuel = 105000.00 WHERE id_service = 1 AND type_solde = 'CAISSE';
UPDATE solde_service SET montant_actuel = 15000.00 WHERE id_service = 2 AND type_solde = 'FLOAT';
UPDATE solde_service SET montant_actuel = 55000.00 WHERE id_service = 2 AND type_solde = 'CAISSE';
UPDATE solde_service SET montant_actuel = 20000.00 WHERE id_service = 3 AND type_solde = 'FLOAT';
UPDATE solde_service SET montant_actuel = 170000.00 WHERE id_service = 3 AND type_solde = 'CAISSE';
UPDATE solde_service SET montant_actuel = 8000.00 WHERE id_service = 9 AND type_solde = 'FLOAT';
UPDATE solde_service SET montant_actuel = 32000.00 WHERE id_service = 9 AND type_solde = 'CAISSE';
UPDATE solde_service SET montant_actuel = 18000.00 WHERE id_service = 10 AND type_solde = 'FLOAT';
UPDATE solde_service SET montant_actuel = 50000.00 WHERE id_service = 10 AND type_solde = 'CAISSE';
UPDATE solde_service SET montant_actuel = 10000.00 WHERE id_service = 11 AND type_solde = 'FLOAT';
UPDATE solde_service SET montant_actuel = 45000.00 WHERE id_service = 11 AND type_solde = 'CAISSE';

-- ============================================================
-- INDEX pour optimiser les requêtes fréquentes
-- ============================================================
CREATE INDEX idx_transaction_service   ON transaction(id_service);
CREATE INDEX idx_transaction_date      ON transaction(created_at);
CREATE INDEX idx_transaction_user      ON transaction(id_user);
CREATE INDEX idx_mouvement_transaction ON mouvement_solde(id_transaction);
CREATE INDEX idx_mouvement_solde       ON mouvement_solde(id_solde);
CREATE INDEX idx_commission_transaction ON commission_transaction(id_transaction);
CREATE INDEX idx_alerte_statut         ON alerte_solde(statut);

-- ============================================================
-- FIN DU SCRIPT
-- ============================================================
