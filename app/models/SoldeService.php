<?php
// ============================================================
//  app/models/SoldeService.php — Fichier commenté
// ============================================================

// Classe SoldeService : implémente la logique métier pour cette partie de l’application
class SoldeService extends Model {
    protected string $table      = 'solde_service';
    protected string $primaryKey = 'id_solde';

// Méthode getByService : gère getByService. 
    public function getByService(int $idService, ?int $agencyId = null): array {
        $query = "
            SELECT ss.*, s.nom AS nom_service, s.categorie,
                   sa.valeur_seuil
            FROM solde_service ss
            JOIN service s ON s.id_service = ss.id_service
            LEFT JOIN seuil_alerte sa ON sa.id_solde = ss.id_solde
            WHERE ss.id_service = ?
        ";
        $params = [$idService];
        if ($agencyId !== null) {
            $query .= " AND ss.id_agence = ?";
            $params[] = $agencyId;
        }
        return $this->query($query, $params);
    }

// Méthode getSolde : gère getSolde. 
    public function getSolde(int $idService, string $typeSolde, ?int $agencyId = null): ?array {
        $sql = "SELECT * FROM solde_service WHERE id_service = ? AND type_solde = ?";
        $params = [$idService, $typeSolde];
        if ($agencyId !== null) {
            $sql .= " AND id_agence = ?";
            $params[] = $agencyId;
        }
        return $this->queryOne($sql, $params);
    }

// Méthode getAllAvecSeuils : gère getAllAvecSeuils. 
    public function getAllAvecSeuils(?int $agencyId = null): array {
        $query = "
            SELECT ss.*, s.nom AS nom_service, s.categorie,
                   sa.valeur_seuil,
                   CASE WHEN ss.montant_actuel < sa.valeur_seuil
                        THEN 1 ELSE 0 END AS en_alerte
            FROM solde_service ss
            JOIN service s       ON s.id_service    = ss.id_service
            LEFT JOIN seuil_alerte sa ON sa.id_solde = ss.id_solde
            WHERE 1=1
        ";
        $params = [];
        if ($agencyId !== null) {
            $query .= " AND ss.id_agence = ?";
            $params[] = $agencyId;
        }
        $query .= " ORDER BY s.categorie, s.nom, ss.type_solde";
        return $this->query($query, $params);
    }

// Méthode getDisponibilitePourcentage : part des soldes actifs disponibles.
    public function getDisponibilitePourcentage(): int {
        $result = $this->queryOne("
            SELECT
                (SELECT COUNT(*) * 2 FROM service WHERE actif = 1) AS total_soldes,
                (
                    SELECT COUNT(*)
                    FROM solde_service ss
                    JOIN service s ON s.id_service = ss.id_service
                    WHERE s.actif = 1
                      AND ss.montant_actuel > 0
                ) AS soldes_disponibles
        ");

        $total = (int)($result['total_soldes'] ?? 0);
        if ($total === 0) {
            return 0;
        }

        $disponibles = (int)($result['soldes_disponibles'] ?? 0);
        return (int)round(($disponibles / $total) * 100);
    }

// Méthode mettreAJour : gère mettreAJour. 
    public function mettreAJour(int $idSolde, float $variation, string $nature): array {
        // Verrouille la ligne pendant les écritures financières concurrentes.
        $solde = $this->queryOne("
            SELECT *
            FROM solde_service
            WHERE id_solde = ?
            FOR UPDATE
        ", [$idSolde]);
        if (!$solde) {
            throw new RuntimeException('Solde introuvable.');
        }

        $soldeAvant = (float) $solde['montant_actuel'];
        $soldeApres = $nature === 'CREDIT'
            ? $soldeAvant + $variation
            : $soldeAvant - $variation;

        // Mettre à jour
        $this->update($idSolde, ['montant_actuel' => $soldeApres]);

        return [
            'solde_avant' => $soldeAvant,
            'solde_apres' => $soldeApres,
        ];
    }
}
