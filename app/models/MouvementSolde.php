<?php
// ============================================================
//  app/models/MouvementSolde.php — Fichier commenté
// ============================================================

// Classe MouvementSolde : implémente la logique métier pour cette partie de l’application
class MouvementSolde extends Model {
    protected string $table      = 'mouvement_solde';
    protected string $primaryKey = 'id_mouvement';

// Méthode createMouvement : gère createMouvement. 
    public function createMouvement(int $idTransaction, int $idSolde, string $nature,
                                    float $montant, float $soldeAvant, float $soldeApres,
                                    string $motif = ''): int {
        return $this->create([
            'id_transaction' => $idTransaction,
            'id_solde'       => $idSolde,
            'nature'         => $nature,
            'montant'        => $montant,
            'solde_avant'    => $soldeAvant,
            'solde_apres'    => $soldeApres,
            'motif'          => $motif,
        ]);
    }

// Méthode getByTransaction : gère getByTransaction. 
    public function getByTransaction(int $idTransaction): array {
        return $this->query("
            SELECT ms.*, ss.type_solde, s.nom AS nom_service
            FROM mouvement_solde ms
            JOIN solde_service ss ON ss.id_solde   = ms.id_solde
            JOIN service       s  ON s.id_service  = ss.id_service
            WHERE ms.id_transaction = ?
        ", [$idTransaction]);
    }
}
