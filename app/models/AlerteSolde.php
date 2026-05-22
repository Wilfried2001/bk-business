<?php
class AlerteSolde extends Model {
    protected string $table      = 'alerte_solde';
    protected string $primaryKey = 'id_alerte';

    public function getActives(): array {
        return $this->query("
            SELECT al.*, sa.valeur_seuil,
                   ss.type_solde, ss.montant_actuel,
                   s.nom AS nom_service
            FROM alerte_solde al
            JOIN seuil_alerte  sa ON sa.id_seuil   = al.id_seuil
            JOIN solde_service  ss ON ss.id_solde   = sa.id_solde
            JOIN service        s  ON s.id_service  = ss.id_service
            WHERE al.statut = 'ACTIVE'
            ORDER BY al.date_alerte DESC
        ");
    }

    public function compterActives(): int {
        $r = $this->queryOne("SELECT COUNT(*) AS nb FROM alerte_solde WHERE statut = 'ACTIVE'");
        return (int)($r['nb'] ?? 0);
    }

    public function traiter(int $idAlerte, int $idUser): bool {
        return $this->update($idAlerte, [
            'statut'          => 'TRAITEE',
            'traite_par'      => $idUser,
            'date_traitement' => date('Y-m-d H:i:s'),
        ]);
    }
}
