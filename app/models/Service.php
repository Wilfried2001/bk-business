<?php
class Service extends Model {
    protected string $table      = 'service';
    protected string $primaryKey = 'id_service';

    public function getAllActifs(): array {
        return $this->query("SELECT * FROM service WHERE actif = 1 ORDER BY categorie, nom");
    }

    public function getByCategorie(string $categorie): array {
        return $this->query("SELECT * FROM service WHERE categorie = ? AND actif = 1", [$categorie]);
    }
}
