<?php
class Transaction extends Model {
    protected string $table      = 'transaction';
    protected string $primaryKey = 'id_transaction';

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

    public function getTotalJour(): float {
        $result = $this->queryOne("
            SELECT COALESCE(SUM(montant), 0) AS total
            FROM transaction
            WHERE DATE(date_heure) = CURDATE() AND statut = 'VALIDEE'
        ");
        return (float) ($result['total'] ?? 0);
    }

    public function getNbJour(): int {
        $result = $this->queryOne("
            SELECT COUNT(*) AS nb
            FROM transaction
            WHERE DATE(date_heure) = CURDATE() AND statut = 'VALIDEE'
        ");
        return (int) ($result['nb'] ?? 0);
    }
}
