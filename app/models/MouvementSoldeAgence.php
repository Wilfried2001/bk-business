<?php
// ============================================================
//  app/models/MouvementSoldeAgence.php
// ============================================================

class MouvementSoldeAgence extends Model {
    protected string $table      = 'mouvement_solde_agence';
    protected string $primaryKey = 'id_mouvement';

    public function createMouvement(int $idTransfert, int $idSolde, string $nature,
                                    float $montant, float $soldeAvant, float $soldeApres,
                                    string $motif = ''): int {
        return $this->create([
            'id_transfert' => $idTransfert,
            'id_solde'     => $idSolde,
            'nature'       => $nature,
            'montant'      => $montant,
            'solde_avant'  => $soldeAvant,
            'solde_apres'  => $soldeApres,
            'motif'        => $motif,
        ]);
    }

    public function getByTransfert(int $idTransfert): array {
        return $this->query(
            "SELECT msa.*, ass.type_solde, s.nom AS nom_service, a.nom AS nom_agence
             FROM mouvement_solde_agence msa
             JOIN agence_solde_service ass ON ass.id_solde = msa.id_solde
             JOIN service s ON s.id_service = ass.id_service
             JOIN agence a ON a.id_agence = ass.id_agence
             WHERE msa.id_transfert = ?
             ORDER BY msa.id_mouvement ASC",
            [$idTransfert]
        );
    }
}
