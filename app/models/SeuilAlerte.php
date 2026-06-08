<?php
// ============================================================
//  app/models/SeuilAlerte.php — Fichier commenté
// ============================================================

// Classe SeuilAlerte : implémente la logique métier pour cette partie de l’application
class SeuilAlerte extends Model {
    protected string $table      = 'seuil_alerte';
    protected string $primaryKey = 'id_seuil';

// Méthode getBySolde : gère getBySolde. 
    public function getBySolde(int $idSolde): ?array {
        return $this->queryOne("SELECT * FROM seuil_alerte WHERE id_solde = ?", [$idSolde]);
    }

// Méthode estAtteint : gère estAtteint. 
    public function estAtteint(int $idSolde, float $montantActuel): bool {
        $seuil = $this->getBySolde($idSolde);
        return $seuil && $seuil['actif'] && $montantActuel < (float) $seuil['valeur_seuil'];
    }

// Méthode saveForSolde : gère saveForSolde. 
    public function saveForSolde(int $idSolde, float $valeurSeuil, ?int $modifiePar = null): int {
        $this->ensureHistoryTableExists();
        $this->beginTransaction();

        try {
            $seuil = $this->getBySolde($idSolde);
            if ($seuil) {
                $ancienneValeur = (float) $seuil['valeur_seuil'];
                $this->update((int)$seuil['id_seuil'], [
                    'valeur_seuil' => $valeurSeuil,
                    'actif'        => 1,
                ]);
                $idSeuil = (int)$seuil['id_seuil'];
            } else {
                $idSeuil = $this->create([
                    'id_solde'     => $idSolde,
                    'valeur_seuil' => $valeurSeuil,
                    'actif'        => 1,
                ]);
                $ancienneValeur = 0.0;
            }

            $this->execute(
                "INSERT INTO seuil_alerte_historique (id_seuil, id_user, ancienne_valeur, nouvelle_valeur, date_modification)
                 VALUES (:id_seuil, :id_user, :ancienne_valeur, :nouvelle_valeur, :date_modification)",
                [
                    'id_seuil'          => $idSeuil,
                    'id_user'           => $modifiePar,
                    'ancienne_valeur'   => $ancienneValeur,
                    'nouvelle_valeur'   => $valeurSeuil,
                    'date_modification' => date('Y-m-d H:i:s'),
                ]
            );

            $this->commit();
            return $idSeuil;
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

// Méthode ensureHistoryTableExists : gère ensureHistoryTableExists. 
    private function ensureHistoryTableExists(): void {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS seuil_alerte_historique (
                id_historique BIGINT NOT NULL AUTO_INCREMENT,
                id_seuil BIGINT NOT NULL,
                id_user BIGINT NOT NULL,
                ancienne_valeur DECIMAL(15,2) NOT NULL,
                nouvelle_valeur DECIMAL(15,2) NOT NULL,
                date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id_historique),
                CONSTRAINT fk_historique_seuil FOREIGN KEY (id_seuil) REFERENCES seuil_alerte(id_seuil),
                CONSTRAINT fk_historique_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

// Méthode getHistoryByService : gère getHistoryByService. 
    public function getHistoryByService(int $idService): array {
        return $this->query(
            "SELECT h.*, ss.type_solde, u.nom AS modifie_par_nom
             FROM seuil_alerte_historique h
             JOIN seuil_alerte s ON s.id_seuil = h.id_seuil
             JOIN solde_service ss ON ss.id_solde = s.id_solde
             LEFT JOIN utilisateur u ON u.id_user = h.id_user
             WHERE ss.id_service = ?
             ORDER BY h.date_modification DESC",
            [$idService]
        );
    }
}
