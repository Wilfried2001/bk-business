<?php
// ============================================================
//  app/models/Agence.php
// ============================================================

class Agence extends Model {
    protected string $table      = 'agence';
    protected string $primaryKey = 'id_agence';

    public function getAllActives(): array {
        return $this->query("SELECT * FROM agence WHERE actif = 1 ORDER BY ville, nom");
    }
}
