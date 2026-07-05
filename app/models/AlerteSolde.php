<?php
// ============================================================
//  app/models/AlerteSolde.php — Fichier commenté
// ============================================================

// Classe AlerteSolde : implémente la logique métier pour cette partie de l’application
class AlerteSolde extends Model {
    protected string $table      = 'alerte_solde';
    protected string $primaryKey = 'id_alerte';

// Méthode getActives : gère getActives. 
    public function getActives(?int $agencyId = null): array {
        $query = "
            SELECT al.*, sa.valeur_seuil,
                   ss.type_solde, ss.montant_actuel,
                   s.nom AS nom_service
            FROM alerte_solde al
            JOIN seuil_alerte  sa ON sa.id_seuil   = al.id_seuil
            JOIN solde_service  ss ON ss.id_solde   = sa.id_solde
            JOIN service        s  ON s.id_service  = ss.id_service
            WHERE al.statut = 'ACTIVE'
        ";
        $params = [];
        if ($agencyId !== null) {
            $query .= " AND ss.id_agence = ?";
            $params[] = $agencyId;
        }
        $query .= " ORDER BY al.date_alerte DESC";
        return $this->query($query, $params);
    }

// Méthode compterActives : gère compterActives. 
    public function compterActives(?int $agencyId = null): int {
        $sql = "SELECT COUNT(*) AS nb FROM alerte_solde al JOIN seuil_alerte sa ON sa.id_seuil = al.id_seuil JOIN solde_service ss ON ss.id_solde = sa.id_solde WHERE al.statut = 'ACTIVE'";
        $params = [];
        if ($agencyId !== null) {
            $sql .= " AND ss.id_agence = ?";
            $params[] = $agencyId;
        }
        $r = $this->queryOne($sql, $params);
        return (int)($r['nb'] ?? 0);
    }

// Méthode getTopServicesByAlertCount : services avec le plus d'alertes actives.
    public function getTopServicesByAlertCount(int $limit = 5, ?int $agencyId = null): array {
        $sql = "
            SELECT s.id_service, s.nom AS nom_service, s.categorie,
                   COUNT(*) AS active_alerts
            FROM alerte_solde al
            JOIN seuil_alerte sa ON sa.id_seuil = al.id_seuil
            JOIN solde_service ss ON ss.id_solde = sa.id_solde
            JOIN service s ON s.id_service = ss.id_service
            WHERE al.statut = 'ACTIVE'
        ";
        $params = [];
        if ($agencyId !== null) {
            $sql .= " AND ss.id_agence = ?";
            $params[] = $agencyId;
        }
        $sql .= " GROUP BY s.id_service, s.nom, s.categorie ORDER BY active_alerts DESC LIMIT ?";
        $params[] = $limit;
        return $this->query($sql, $params);
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
