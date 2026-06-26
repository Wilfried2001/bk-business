-- Champs requis pour les transactions internationales.
-- A executer sur une base deja installee avant d'utiliser le nouveau wizard.

ALTER TABLE transaction
    ADD COLUMN expediteur_identifiant VARCHAR(100) NULL AFTER nom_expediteur,
    ADD COLUMN expediteur_telephone VARCHAR(50) NULL AFTER expediteur_identifiant,
    ADD COLUMN beneficiaire_identifiant VARCHAR(100) NULL AFTER nom_benefis,
    ADD COLUMN beneficiaire_telephone VARCHAR(50) NULL AFTER beneficiaire_identifiant;
