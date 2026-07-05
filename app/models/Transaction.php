<?php
// ============================================================
//  app/models/Transaction.php — Fichier commenté
// ============================================================

// Classe Transaction : implémente la logique métier pour cette partie de l’application
class Transaction extends Model {
    protected string $table      = 'transaction';
    protected string $primaryKey = 'id_transaction';

// Méthode getAllWithDetails : gère getAllWithDetails. 
    public function getAllWithDetails(array $filtres = []): array {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filtres['id_service'])) {
            $where[]  = 't.id_service = ?';
            $params[] = $filtres['id_service'];
        }
        if (!empty($filtres['date_debut'])) {
            $where[]  = 'DATE(t.created_at) >= ?';
            $params[] = $filtres['date_debut'];
        }
        if (!empty($filtres['date_fin'])) {
            $where[]  = 'DATE(t.created_at) <= ?';
            $params[] = $filtres['date_fin'];
        }
        if (!empty($filtres['id_type'])) {
            $where[]  = 't.id_type = ?';
            $params[] = $filtres['id_type'];
        }
        if (!empty($filtres['id_agence'])) {
            $where[]  = 't.id_agence = ?';
            $params[] = $filtres['id_agence'];
        }
        if (!empty($filtres['statut'])) {
            $where[]  = 't.statut = ?';
            $params[] = $filtres['statut'];
        }
        if (!empty($filtres['search'])) {
            $where[]  = '(t.reference LIKE ? OR u.nom LIKE ? OR s.nom LIKE ? OR to2.libelle LIKE ? OR t.nom_expediteur LIKE ? OR t.nom_benefis LIKE ? OR a.nom LIKE ?)';
            $search = '%' . $filtres['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filtres['exclude_adjustments'])) {
            $where[] = "to2.libelle != '" . TypeOperation::ADJUSTMENT_LABEL . "'";
        }

        $whereStr = implode(' AND ', $where);

        $limitClause = '';
        if (!empty($filtres['limit']) && is_numeric($filtres['limit']) && (int)$filtres['limit'] > 0) {
            $limitClause = ' LIMIT ?';
            $params[] = (int)$filtres['limit'];
        }

        return $this->query("
            SELECT t.*, t.created_at AS date_heure,
                   s.nom          AS nom_service,
                   to2.libelle    AS libelle_type,
                   to2.impact_float, to2.impact_caisse,
                   u.nom          AS nom_agent,
                   a.nom          AS nom_agence
            FROM transaction t
            JOIN service        s   ON s.id_service = t.id_service
            JOIN type_operation to2 ON to2.id_type  = t.id_type
            JOIN utilisateur    u   ON u.id_user    = t.id_user
            LEFT JOIN agence    a   ON a.id_agence  = t.id_agence
            WHERE {$whereStr}
            ORDER BY t.created_at DESC" . $limitClause . "\n        ", $params);
    }

// Méthode getTopServicesByUsage : services les plus utilisés.
    public function getTopServicesByUsage(int $limit = 5, ?int $agencyId = null): array {
        $sql = "
            SELECT s.id_service, s.nom AS nom_service, s.categorie,
                   COUNT(*) AS total_transactions,
                   SUM(t.montant) AS total_montant
            FROM transaction t
            JOIN service s ON s.id_service = t.id_service
            JOIN type_operation to2 ON to2.id_type = t.id_type
            WHERE t.statut = 'VALIDEE'
              AND to2.libelle != 'AJUSTEMENT'
        ";
        $params = [];
        if ($agencyId !== null) {
            $sql .= " AND t.id_agence = ?";
            $params[] = $agencyId;
        }
        $sql .= " GROUP BY s.id_service, s.nom, s.categorie ORDER BY total_transactions DESC LIMIT ?";
        $params[] = $limit;
        return $this->query($sql, $params);
    }

// Méthode getTopServicesByMontant : services les plus lourds en montant.
    public function getTopServicesByMontant(int $limit = 5, ?int $agencyId = null): array {
        $sql = "
            SELECT s.id_service, s.nom AS nom_service, s.categorie,
                   COUNT(*) AS total_transactions,
                   SUM(t.montant) AS total_montant
            FROM transaction t
            JOIN service s ON s.id_service = t.id_service
            JOIN type_operation to2 ON to2.id_type = t.id_type
            WHERE t.statut = 'VALIDEE'
              AND to2.libelle != 'AJUSTEMENT'
        ";
        $params = [];
        if ($agencyId !== null) {
            $sql .= " AND t.id_agence = ?";
            $params[] = $agencyId;
        }
        $sql .= " GROUP BY s.id_service, s.nom, s.categorie ORDER BY total_montant DESC LIMIT ?";
        $params[] = $limit;
        return $this->query($sql, $params);
    }

// Méthode getWithDetails : gère getWithDetails. 
    public function getWithDetails(int $id, ?int $agencyId = null): ?array {
        $sql = "
            SELECT t.*, t.created_at AS date_heure,
                   s.nom       AS nom_service, s.categorie,
                   to2.libelle AS libelle_type,
                   to2.impact_float, to2.impact_caisse,
                   u.nom       AS nom_agent,
                   a.nom       AS nom_agence
            FROM transaction t
            JOIN service        s   ON s.id_service = t.id_service
            JOIN type_operation to2 ON to2.id_type  = t.id_type
            JOIN utilisateur    u   ON u.id_user    = t.id_user
            LEFT JOIN agence    a   ON a.id_agence  = t.id_agence
            WHERE t.id_transaction = ?
        ";
        $params = [$id];
        if ($agencyId !== null) {
            $sql .= " AND t.id_agence = ?";
            $params[] = $agencyId;
        }
        return $this->queryOne($sql, $params);
    }

// Méthode getTotalJour : gère getTotalJour. 
    public function getTotalJour(?int $agencyId = null): float {
        $sql = "
            SELECT COALESCE(SUM(t.montant), 0) AS total
            FROM transaction t
            JOIN type_operation to2 ON to2.id_type = t.id_type
            WHERE DATE(t.created_at) = CURDATE()
              AND t.statut = 'VALIDEE'
              AND to2.libelle != 'AJUSTEMENT'
        ";
        $params = [];
        if ($agencyId !== null) {
            $sql .= " AND t.id_agence = ?";
            $params[] = $agencyId;
        }
        $result = $this->queryOne($sql, $params);
        return (float) ($result['total'] ?? 0);
    }

// Méthode getNbJour : gère getNbJour. 
    public function getNbJour(?int $agencyId = null): int {
        $sql = "
            SELECT COUNT(*) AS nb
            FROM transaction t
            JOIN type_operation to2 ON to2.id_type = t.id_type
            WHERE DATE(t.created_at) = CURDATE()
              AND t.statut = 'VALIDEE'
              AND to2.libelle != 'AJUSTEMENT'
        ";
        $params = [];
        if ($agencyId !== null) {
            $sql .= " AND t.id_agence = ?";
            $params[] = $agencyId;
        }
        $result = $this->queryOne($sql, $params);
        return (int) ($result['nb'] ?? 0);
    }
}
