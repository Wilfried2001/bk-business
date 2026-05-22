<?php
class SeuilAlerte extends Model {
    protected string $table      = 'seuil_alerte';
    protected string $primaryKey = 'id_seuil';

    public function getBySolde(int $idSolde): ?array {
        return $this->queryOne("SELECT * FROM seuil_alerte WHERE id_solde = ?", [$idSolde]);
    }

    public function estAtteint(int $idSolde, float $montantActuel): bool {
        $seuil = $this->getBySolde($idSolde);
        return $seuil && $seuil['actif'] && $montantActuel < (float) $seuil['valeur_seuil'];
    }

    public function saveForSolde(int $idSolde, float $valeurSeuil): int {
        $seuil = $this->getBySolde($idSolde);
        if ($seuil) {
            $this->update((int)$seuil['id_seuil'], [
                'valeur_seuil' => $valeurSeuil,
                'actif'        => 1,
            ]);
            return (int)$seuil['id_seuil'];
        }

        return $this->create([
            'id_solde'     => $idSolde,
            'valeur_seuil' => $valeurSeuil,
            'actif'        => 1,
        ]);
    }
}
