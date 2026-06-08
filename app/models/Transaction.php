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
            $where[]  = 'DATE(t.date_heure) >= ?';
            $params[] = $filtres['date_debut'];
        }
        if (!empty($filtres['date_fin'])) {
            $where[]  = 'DATE(t.date_heure) <= ?';
            $params[] = $filtres['date_fin'];
        }
        if (!empty($filtres['id_type'])) {
            $where[]  = 't.id_type = ?';
            $params[] = $filtres['id_type'];
        }
        if (!empty($filtres['statut'])) {
            $where[]  = 't.statut = ?';
            $params[] = $filtres['statut'];
        }
        if (!empty($filtres['search'])) {
            $where[]  = '(t.reference LIKE ? OR u.nom LIKE ? OR s.nom LIKE ? OR to2.libelle LIKE ?)';
            $search = '%' . $filtres['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $whereStr = implode(' AND ', $where);

        return $this->query("
            SELECT t.*,
                   s.nom          AS nom_service,
                   to2.libelle    AS libelle_type,
                   to2.impact_float, to2.impact_caisse,
                   u.nom          AS nom_agent
            FROM transaction t
            JOIN service        s   ON s.id_service = t.id_service
            JOIN type_operation to2 ON to2.id_type  = t.id_type
            JOIN utilisateur    u   ON u.id_user    = t.id_user
            WHERE {$whereStr}
            ORDER BY t.date_heure DESC
        ", $params);
    }

// Méthode getTopServicesByUsage : services les plus utilisés.
    public function getTopServicesByUsage(int $limit = 5): array {
        return $this->query(
            "
            SELECT s.id_service, s.nom AS nom_service, s.categorie,
                   COUNT(*) AS total_transactions,
                   SUM(t.montant) AS total_montant
            FROM transaction t
            JOIN service s ON s.id_service = t.id_service
            WHERE t.statut = 'VALIDEE'
            GROUP BY s.id_service, s.nom, s.categorie
            ORDER BY total_transactions DESC
            LIMIT ?
        ", [$limit]);
    }

// Méthode getTopServicesByMontant : services les plus lourds en montant.
    public function getTopServicesByMontant(int $limit = 5): array {
        return $this->query(
            "
            SELECT s.id_service, s.nom AS nom_service, s.categorie,
                   COUNT(*) AS total_transactions,
                   SUM(t.montant) AS total_montant
            FROM transaction t
            JOIN service s ON s.id_service = t.id_service
            WHERE t.statut = 'VALIDEE'
            GROUP BY s.id_service, s.nom, s.categorie
            ORDER BY total_montant DESC
            LIMIT ?
        ", [$limit]);
    }

// Méthode getWithDetails : gère getWithDetails. 
    public function getWithDetails(int $id): ?array {
        return $this->queryOne("
            SELECT t.*,
                   s.nom       AS nom_service, s.categorie,
                   to2.libelle AS libelle_type,
                   to2.impact_float, to2.impact_caisse,
                   u.nom       AS nom_agent
            FROM transaction t
            JOIN service        s   ON s.id_service = t.id_service
            JOIN type_operation to2 ON to2.id_type  = t.id_type
            JOIN utilisateur    u   ON u.id_user    = t.id_user
            WHERE t.id_transaction = ?
        ", [$id]);
    }

// Méthode getTotalJour : gère getTotalJour. 
    public function getTotalJour(): float {
        $result = $this->queryOne("
            SELECT COALESCE(SUM(montant), 0) AS total
            FROM transaction
            WHERE DATE(date_heure) = CURDATE() AND statut = 'VALIDEE'
        ");
        return (float) ($result['total'] ?? 0);
    }

// Méthode getNbJour : gère getNbJour. 
    public function getNbJour(): int {
        $result = $this->queryOne("
            SELECT COUNT(*) AS nb
            FROM transaction
            WHERE DATE(date_heure) = CURDATE() AND statut = 'VALIDEE'
        ");
        return (int) ($result['nb'] ?? 0);
    }
}