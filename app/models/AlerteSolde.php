<?php
// ============================================================
//  app/models/AlerteSolde.php — Fichier commenté
// ============================================================

// Classe AlerteSolde : implémente la logique métier pour cette partie de l’application
class AlerteSolde extends Model {
    protected string $table      = 'alerte_solde';
    protected string $primaryKey = 'id_alerte';

// Méthode getActives : gère getActives. 
    public function getActives(): array {
        return $this->query("
            SELECT al.*, sa.valeur_seuil,
                   ss.type_solde, ss.montant_actuel,
                   s.nom AS nom_service
            FROM alerte_solde al
            JOIN seuil_alerte  sa ON sa.id_seuil   = al.id_seuil
            JOIN solde_service  ss ON ss.id_solde   = sa.id_solde
            JOIN service        s  ON s.id_service  = ss.id_service
            WHERE al.statut = 'ACTIVE'
            ORDER BY al.date_alerte DESC
        ");
    }

// Méthode compterActives : gère compterActives. 
    public function compterActives(): int {
        $r = $this->queryOne("SELECT COUNT(*) AS nb FROM alerte_solde WHERE statut = 'ACTIVE'");
        return (int)($r['nb'] ?? 0);
    }

// Méthode getTopServicesByAlertCount : services avec le plus d'alertes actives.
    public function getTopServicesByAlertCount(int $limit = 5): array {
        return $this->query(
            "
            SELECT s.id_service, s.nom AS nom_service, s.categorie,
                   COUNT(*) AS active_alerts
            FROM alerte_solde al
            JOIN seuil_alerte sa ON sa.id_seuil = al.id_seuil
            JOIN solde_service ss ON ss.id_solde = sa.id_solde
            JOIN service s ON s.id_service = ss.id_service
            WHERE al.statut = 'ACTIVE'
            GROUP BY s.id_service, s.nom, s.categorie
            ORDER BY active_alerts DESC
            LIMIT ?
        ", [$limit]);
    }

// Méthode traiter : gère traiter. 
    public function traiter(int $idAlerte, int $idUser): bool {
        return $this->update($idAlerte, [
            'statut'          => 'TRAITEE',
            'traite_par'      => $idUser,
            'date_traitement' => date('Y-m-d H:i:s'),
        ]);
    }
}
