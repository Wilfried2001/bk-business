-- Migration: Ajouter colonne id_agence (nullable) sur tables critiques
-- Exécuter en staging d'abord. Faire une sauvegarde complète avant.

START TRANSACTION;

-- Transaction
ALTER TABLE `transaction`
  ADD COLUMN `id_agence` INT NULL AFTER `id_user`,
  ADD INDEX `idx_transaction_id_agence` (`id_agence`);

-- Solde service
ALTER TABLE `solde_service`
  ADD COLUMN `id_agence` INT NULL AFTER `id_service`,
  ADD INDEX `idx_solde_service_id_agence` (`id_agence`);

-- Mouvement solde
ALTER TABLE `mouvement_solde`
  ADD COLUMN `id_agence` INT NULL AFTER `id_solde`,
  ADD INDEX `idx_mouvement_solde_id_agence` (`id_agence`);

-- Alerte solde
ALTER TABLE `alerte_solde`
  ADD COLUMN `id_agence` INT NULL AFTER `id_seuil`,
  ADD INDEX `idx_alerte_solde_id_agence` (`id_agence`);

-- Commission transaction
ALTER TABLE `commission_transaction`
  ADD COLUMN `id_agence` INT NULL AFTER `id_transaction`,
  ADD INDEX `idx_commission_transaction_id_agence` (`id_agence`);

-- Optionnel: liaison FK (ajoutez s'il existe déjà une table `agence` avec PK `id_agence`)
ALTER TABLE `transaction`
  ADD CONSTRAINT `fk_transaction_agence` FOREIGN KEY (`id_agence`) REFERENCES `agence` (`id_agence`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `solde_service`
  ADD CONSTRAINT `fk_solde_service_agence` FOREIGN KEY (`id_agence`) REFERENCES `agence` (`id_agence`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `mouvement_solde`
  ADD CONSTRAINT `fk_mouvement_solde_agence` FOREIGN KEY (`id_agence`) REFERENCES `agence` (`id_agence`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `alerte_solde`
  ADD CONSTRAINT `fk_alerte_solde_agence` FOREIGN KEY (`id_agence`) REFERENCES `agence` (`id_agence`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `commission_transaction`
  ADD CONSTRAINT `fk_commission_transaction_agence` FOREIGN KEY (`id_agence`) REFERENCES `agence` (`id_agence`) ON DELETE SET NULL ON UPDATE CASCADE;

COMMIT;
