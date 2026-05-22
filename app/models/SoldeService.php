<?php
class SoldeService extends Model {
    protected string $table      = 'solde_service';
    protected string $primaryKey = 'id_solde';

    public function getByService(int $idService): array {
        return $this->query("
            SELECT ss.*, s.nom AS nom_service, s.categorie,
                   sa.valeur_seuil
            FROM solde_service ss
            JOIN service s ON s.id_service = ss.id_service
            LEFT JOIN seuil_alerte sa ON sa.id_solde = ss.id_solde
            WHERE ss.id_service = ?
        ", [$idService]);
    }

    public function getSolde(int $idService, string $typeSolde): ?array {
        return $this->queryOne("
            SELECT * FROM solde_service
            WHERE id_service = ? AND type_solde = ?
        ", [$idService, $typeSolde]);
    }

    public function getAllAvecSeuils(): array {
        return $this->query("
            SELECT ss.*, s.nom AS nom_service, s.categorie,
                   sa.valeur_seuil,
                   CASE WHEN ss.montant_actuel < sa.valeur_seuil
                        THEN 1 ELSE 0 END AS en_alerte
            FROM solde_service ss
            JOIN service s       ON s.id_service    = ss.id_service
            LEFT JOIN seuil_alerte sa ON sa.id_solde = ss.id_solde
            ORDER BY s.categorie, s.nom, ss.type_solde
        ");
    }

    public function mettreAJour(int $idSolde, float $variation, string $nature): array {
        // Récupérer le solde actuel
        $solde = $this->find($idSolde);
        $soldeAvant = (float) $solde['montant_actuel'];
        $soldeApres = $nature === 'CREDIT'
            ? $soldeAvant + $variation
            : $soldeAvant - $variation;

        // Mettre à jour
        $this->update($idSolde, ['montant_actuel' => $soldeApres]);

        return [
            'solde_avant' => $soldeAvant,
            'solde_apres' => $soldeApres,
        ];
    }
}
