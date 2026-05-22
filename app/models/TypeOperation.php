<?php
class TypeOperation extends Model {
    protected string $table      = 'type_operation';
    protected string $primaryKey = 'id_type';

    public function getByService(int $idService): array {
        // Retourne les types d'opération compatibles selon la catégorie du service
        return $this->query("
            SELECT DISTINCT to2.*
            FROM type_operation to2
            JOIN commission_config cc ON cc.id_type = to2.id_type
            WHERE cc.id_service = ? AND cc.actif = 1
        ", [$idService]);
    }
}
