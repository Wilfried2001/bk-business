<?php
// ============================================================
//  app/models/Service.php — Fichier commenté
// ============================================================

// Classe Service : implémente la logique métier pour cette partie de l’application
class Service extends Model {
    protected string $table      = 'service';
    protected string $primaryKey = 'id_service';

// Méthode getAllActifs : gère getAllActifs. 
    public function getAllActifs(): array {
        return $this->query("SELECT * FROM service WHERE actif = 1 ORDER BY categorie, nom");
    }

// Méthode getByCategorie : gère getByCategorie. 
    public function getByCategorie(string $categorie): array {
        return $this->query("SELECT * FROM service WHERE categorie = ? AND actif = 1", [$categorie]);
    }
}
