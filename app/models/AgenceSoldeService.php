<?php
// ============================================================
//  app/models/AgenceSoldeService.php
// ============================================================

class AgenceSoldeService extends Model {
    protected string $table      = 'agence_solde_service';
    protected string $primaryKey = 'id_solde';

    public function getByAgenceService(int $idAgence, int $idService, string $typeSolde): ?array {
        return $this->queryOne(
            "SELECT * FROM agence_solde_service WHERE id_agence = ? AND id_service = ? AND type_solde = ?",
            [$idAgence, $idService, strtoupper($typeSolde)]
        );
    }

    public function getOrCreate(int $idAgence, int $idService, string $typeSolde): array {
        $typeSolde = strtoupper($typeSolde);
        if ($typeSolde !== 'FLOAT' && $typeSolde !== 'CAISSE') {
            throw new InvalidArgumentException('Type de solde invalide.');
        }

        $solde = $this->getByAgenceService($idAgence, $idService, $typeSolde);
        if ($solde) {
            return $solde;
        }

        $idSolde = $this->create([
            'id_agence'      => $idAgence,
            'id_service'     => $idService,
            'type_solde'     => $typeSolde,
            'montant_actuel' => 0.00,
        ]);

        $solde = $this->find($idSolde);
        if (!$solde) {
            throw new RuntimeException('Impossible de créer le solde agence.');
        }

        return $solde;
    }

    public function mettreAJour(int $idSolde, float $variation, string $nature): array {
        $solde = $this->queryOne(
            "SELECT * FROM agence_solde_service WHERE id_solde = ? FOR UPDATE",
            [$idSolde]
        );
        if (!$solde) {
            throw new RuntimeException('Solde agence introuvable.');
        }

        $soldeAvant = (float) $solde['montant_actuel'];
        $soldeApres = $nature === 'CREDIT'
            ? $soldeAvant + $variation
            : $soldeAvant - $variation;

        $this->update($idSolde, ['montant_actuel' => $soldeApres]);

        return [
            'solde_avant' => $soldeAvant,
            'solde_apres' => $soldeApres,
        ];
    }
}
