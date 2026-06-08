<?php
// ============================================================
//  app/models/CommissionTransaction.php — Fichier commenté
// ============================================================

// Classe CommissionTransaction : implémente la logique métier pour cette partie de l’application
class CommissionTransaction extends Model {
    protected string $table      = 'commission_transaction';
    protected string $primaryKey = 'id_commission';

// Méthode getBeneficesParService : gère getBeneficesParService. 
    public function getBeneficesParService(int $mois = 0, int $annee = 0, int $idService = 0): array {
        $mois      = $mois  ?: (int)date('m');
        $annee     = $annee ?: (int)date('Y');
        $where     = ' WHERE mois = ? AND annee = ?';
        $params    = [$mois, $annee];
        if ($idService > 0) {
            $where .= ' AND id_service = ?';
            $params[] = $idService;
        }
        return $this->query("
            SELECT * FROM benefice_service
            {$where}
            ORDER BY total_commission DESC
        ", $params);
    }

// Méthode getTopServicesByCommission : services les plus rentables.
    public function getTopServicesByCommission(int $limit = 5, int $mois = 0, int $annee = 0): array {
        $mois   = $mois  ?: (int)date('m');
        $annee  = $annee ?: (int)date('Y');
        return $this->query(
            "
            SELECT * FROM benefice_service
            WHERE mois = ? AND annee = ?
            ORDER BY total_commission DESC
            LIMIT ?
        ", [$mois, $annee, $limit]);
    }

// Méthode getTotalCommissions : gère getTotalCommissions. 
    public function getTotalCommissions(int $mois = 0, int $annee = 0, int $idService = 0): float {
        $mois      = $mois  ?: (int)date('m');
        $annee     = $annee ?: (int)date('Y');
        $where     = ' WHERE mois = ? AND annee = ?';
        $params    = [$mois, $annee];
        if ($idService > 0) {
            $where .= ' AND id_service = ?';
            $params[] = $idService;
        }
        $r = $this->queryOne("
            SELECT SUM(total_commission) AS total FROM benefice_service
            {$where}
        ", $params);
        return (float)($r['total'] ?? 0);
    }
}
