<?php
// ============================================================
//  app/models/TypeOperation.php — Fichier commenté
// ============================================================

// Classe TypeOperation : implémente la logique métier pour cette partie de l’application
class TypeOperation extends Model {
    public const ADJUSTMENT_LABEL = 'AJUSTEMENT';

    protected string $table      = 'type_operation';
    protected string $primaryKey = 'id_type';

// Méthode getByService : gère getByService. 
    public function getByService(int $idService): array {
        // Retourne les types d'opération compatibles selon la catégorie du service
        return $this->query("
            SELECT DISTINCT to2.*
            FROM type_operation to2
            JOIN commission_config cc ON cc.id_type = to2.id_type
            WHERE cc.id_service = ? AND cc.actif = 1
        ", [$idService]);
    }

// Méthode findByLabel : recherche un type d'opération par libellé.
    public function findByLabel(string $label): ?array {
        return $this->queryOne("
            SELECT * FROM type_operation WHERE libelle = ?
        ", [$label]);
    }

// Méthode getOrCreateAdjustmentType : retourne l'id du type AJUSTEMENT.
    public function getOrCreateAdjustmentType(): int {
        $adjustType = $this->findByLabel(self::ADJUSTMENT_LABEL);
        if ($adjustType) {
            return (int) $adjustType['id_type'];
        }

        return $this->create([
            'libelle'       => self::ADJUSTMENT_LABEL,
            'description'   => 'Transaction d\'ajustement manuel de solde',
            'impact_float'  => 0,
            'impact_caisse' => 0,
        ]);
    }
}
