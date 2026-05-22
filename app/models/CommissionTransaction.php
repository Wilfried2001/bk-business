<?php
class CommissionTransaction extends Model {
    protected string $table      = 'commission_transaction';
    protected string $primaryKey = 'id_commission';

    public function getBeneficesParService(int $mois = 0, int $annee = 0): array {
        $mois  = $mois  ?: (int)date('m');
        $annee = $annee ?: (int)date('Y');
        return $this->query("
            SELECT * FROM benefice_service
            WHERE mois = ? AND annee = ?
            ORDER BY total_commission DESC
        ", [$mois, $annee]);
    }

    public function getTotalCommissions(int $mois = 0, int $annee = 0): float {
        $mois  = $mois  ?: (int)date('m');
        $annee = $annee ?: (int)date('Y');
        $r = $this->queryOne("
            SELECT SUM(total_commission) AS total FROM benefice_service
            WHERE mois = ? AND annee = ?
        ", [$mois, $annee]);
        return (float)($r['total'] ?? 0);
    }
}
