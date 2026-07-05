-- Rollback: Retirer les colonnes id_agence ajoutées
-- Attention: vérifier qu'aucune application n'utilise encore ces colonnes

START TRANSACTION;

ALTER TABLE `commission_transaction` DROP FOREIGN KEY IF EXISTS `fk_commission_transaction_agence`;
ALTER TABLE `alerte_solde` DROP FOREIGN KEY IF EXISTS `fk_alerte_solde_agence`;
ALTER TABLE `mouvement_solde` DROP FOREIGN KEY IF EXISTS `fk_mouvement_solde_agence`;
ALTER TABLE `solde_service` DROP FOREIGN KEY IF EXISTS `fk_solde_service_agence`;
ALTER TABLE `transaction` DROP FOREIGN KEY IF EXISTS `fk_transaction_agence`;

ALTER TABLE `commission_transaction` DROP COLUMN IF EXISTS `id_agence`;
ALTER TABLE `alerte_solde` DROP COLUMN IF EXISTS `id_agence`;
ALTER TABLE `mouvement_solde` DROP COLUMN IF EXISTS `id_agence`;
ALTER TABLE `solde_service` DROP COLUMN IF EXISTS `id_agence`;
ALTER TABLE `transaction` DROP COLUMN IF EXISTS `id_agence`;

COMMIT;
