<?php
// ============================================================
//  app/models/CommissionConfig.php — Fichier commenté
// ============================================================

// Classe CommissionConfig : implémente la logique métier pour cette partie de l’application
class CommissionConfig extends Model {
    protected string $table      = 'commission_config';
    protected string $primaryKey = 'id_config';

// Méthode getConfig : gère getConfig. 
    public function getConfig(int $idService, int $idType): ?array {
        return $this->queryOne("
            SELECT * FROM commission_config
            WHERE id_service = ? AND id_type = ? AND actif = 1
        ", [$idService, $idType]);
    }

// Méthode calculer : gère calculer. 
    public function calculer(array $config, float $montant): float {
        return match($config['mode_calcul']) {
            'TAUX'    => round($montant * (float)$config['valeur'] / 100, 2),
            'FIXE'    => (float)$config['valeur'],
            'TRANCHE' => $this->calculerParTranche((int)$config['id_config'], $montant),
            default   => 0,
        };
    }

// Méthode calculerParTranche : gère calculerParTranche. 
    private function calculerParTranche(int $idConfig, float $montant): float {
        $stmt = Database::getInstance()->getConnection()->prepare("
            SELECT montant_fixe FROM commission_tranche
            WHERE id_config = ?
              AND montant_min <= ?
              AND (montant_max IS NULL OR montant_max >= ?)
            LIMIT 1
        ");
        $stmt->execute([$idConfig, $montant, $montant]);
        $tranche = $stmt->fetch();
        return $tranche ? (float)$tranche['montant_fixe'] : 0;
    }

// Méthode getAllWithDetails : gère getAllWithDetails. 
    public function getAllWithDetails(): array {
        return $this->query("
            SELECT cc.*, s.nom AS nom_service, to2.libelle AS libelle_type
            FROM commission_config cc
            JOIN service        s   ON s.id_service = cc.id_service
            JOIN type_operation to2 ON to2.id_type  = cc.id_type
            ORDER BY s.nom, to2.libelle
        ");
    }

// Méthode getTranchesByConfig : gère getTranchesByConfig. 
    public function getTranchesByConfig(int $idConfig): array {
        return $this->query(
            "SELECT montant_min, montant_max, montant_fixe
             FROM commission_tranche
             WHERE id_config = ?
             ORDER BY montant_min ASC",
            [$idConfig]
        );
    }

// Méthode clearTranches : gère clearTranches. 
    public function clearTranches(int $idConfig): bool {
        return $this->execute("DELETE FROM commission_tranche WHERE id_config = ?", [$idConfig]);
    }

// Méthode saveTranches : gère saveTranches. 
    public function saveTranches(int $idConfig, array $tranches): bool {
        $this->beginTransaction();
        try {
            $this->clearTranches($idConfig);
            foreach ($tranches as $tranche) {
                $min = isset($tranche['montant_min']) ? trim($tranche['montant_min']) : '';
                $fixe = isset($tranche['montant_fixe']) ? trim($tranche['montant_fixe']) : '';
                if ($min === '' || $fixe === '') {
                    continue;
                }

                $montantMin = (float) $min;
                $montantMax = $tranche['montant_max'] !== '' ? (float) $tranche['montant_max'] : null;
                $montantFixe = (float) $fixe;

                $this->execute(
                    "INSERT INTO commission_tranche (id_config, montant_min, montant_max, montant_fixe) VALUES (?, ?, ?, ?)",
                    [$idConfig, $montantMin, $montantMax, $montantFixe]
                );
            }
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }
}
