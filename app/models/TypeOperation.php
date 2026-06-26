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
        $service = $this->queryOne("SELECT nom, categorie FROM service WHERE id_service = ?", [$idService]);
        if (!$service) {
            return [];
        }

        $labels = $this->labelsForService($service);
        $configured = $this->query("
            SELECT DISTINCT to2.*
            FROM type_operation to2
            JOIN commission_config cc ON cc.id_type = to2.id_type
            WHERE cc.id_service = ? AND cc.actif = 1
            ORDER BY to2.libelle
        ", [$idService]);

        $mapped = [];
        if (!empty($labels)) {
            $placeholders = implode(',', array_fill(0, count($labels), '?'));
            $mapped = $this->query("
                SELECT *
                FROM type_operation
                WHERE libelle IN ({$placeholders})
                ORDER BY FIELD(libelle, {$placeholders})
            ", array_merge($labels, $labels));
        }

        $merged = [];
        foreach (array_merge($mapped, $configured) as $type) {
            $merged[(int)$type['id_type']] = $type;
        }

        if (!empty($merged)) {
            return array_values($merged);
        }

        return $this->query("SELECT * FROM type_operation WHERE libelle != ? ORDER BY libelle", [self::ADJUSTMENT_LABEL]);
    }

    private function labelsForService(array $service): array {
        $name = strtolower((string)($service['nom'] ?? ''));
        $category = strtoupper((string)($service['categorie'] ?? ''));

        if ($category === 'INTERNATIONAL'
            || str_contains($name, 'ria')
            || str_contains($name, 'western')
            || str_contains($name, 'moneygram')
            || str_contains($name, 'cash')
            || str_contains($name, 'xpress')) {
            return ['Envoi client', 'Retrait client', 'Retour de fond', 'Annulation'];
        }

        if ($category === 'MOBILE_MONEY'
            || str_contains($name, 'orange')
            || str_contains($name, 'mtn')) {
            return ['Depot', 'Retrait', 'Paiement', 'Cash in float', 'Cash out float'];
        }

        if (str_contains($name, 'dhl')) {
            return ['Depot', 'Envoi colis', 'Paiement'];
        }

        if ($category === 'ANNEXE'
            || str_contains($name, 'canal')
            || str_contains($name, 'eneo')
            || str_contains($name, 'scolar')) {
            return ['Paiement', 'Reabonnement', 'Depot en especes'];
        }

        return [];
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
