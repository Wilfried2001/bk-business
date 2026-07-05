<?php
// ============================================================
//  app/models/TransfertFloat.php
// ============================================================

class TransfertFloat extends Model {
    protected string $table      = 'transfert_float';
    protected string $primaryKey = 'id_transfert';

    public function getAll(array $filtres = []): array {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtres['id_demande_par'])) {
            $where[] = 'tf.id_demande_par = ?';
            $params[] = $filtres['id_demande_par'];
        }

        if (!empty($filtres['statut'])) {
            $where[] = 'tf.statut = ?';
            $params[] = $filtres['statut'];
        }

        if (!empty($filtres['id_agence'])) {
            $where[] = '(tf.id_agence_source = ? OR tf.id_agence_destination = ?)';
            $params[] = $filtres['id_agence'];
            $params[] = $filtres['id_agence'];
        }

        $whereStr = implode(' AND ', $where);

        return $this->query(
            "SELECT tf.*, a1.nom AS agence_source, a2.nom AS agence_destination,
                    s.nom AS nom_service, u.nom AS demandeur, uv.nom AS valideur
             FROM transfert_float tf
             JOIN agence a1 ON a1.id_agence = tf.id_agence_source
             JOIN agence a2 ON a2.id_agence = tf.id_agence_destination
             JOIN service s ON s.id_service = tf.id_service
             JOIN utilisateur u ON u.id_user = tf.id_demande_par
             LEFT JOIN utilisateur uv ON uv.id_user = tf.id_valide_par
             WHERE {$whereStr}
             ORDER BY tf.created_at DESC",
            $params
        );
    }

    public function getById(int $id): ?array {
        return $this->queryOne(
            "SELECT tf.*, a1.nom AS agence_source, a2.nom AS agence_destination,
                    s.nom AS nom_service, u.nom AS demandeur, uv.nom AS valideur
             FROM transfert_float tf
             JOIN agence a1 ON a1.id_agence = tf.id_agence_source
             JOIN agence a2 ON a2.id_agence = tf.id_agence_destination
             JOIN service s ON s.id_service = tf.id_service
             JOIN utilisateur u ON u.id_user = tf.id_demande_par
             LEFT JOIN utilisateur uv ON uv.id_user = tf.id_valide_par
             WHERE tf.id_transfert = ?",
            [$id]
        );
    }

    public function createRequest(array $data): int {
        return $this->create([
            'id_agence_source'      => $data['id_agence_source'],
            'id_agence_destination' => $data['id_agence_destination'],
            'id_service'            => $data['id_service'],
            'id_demande_par'        => $data['id_demande_par'],
            'montant'               => $data['montant'],
            'motif'                 => $data['motif'] ?? null,
            'statut'                => 'EN_ATTENTE',
        ]);
    }

    public function reject(int $idTransfert, int $idValidePar, string $commentaire): void {
        $this->update($idTransfert, [
            'statut'       => 'REFUSEE',
            'id_valide_par'=> $idValidePar,
            'commentaire'  => $commentaire,
        ]);
    }

    public function executeTransfer(int $idTransfert, int $idValidePar): void {
        $transfer = $this->getById($idTransfert);
        if (!$transfer) {
            throw new InvalidArgumentException('Transfert introuvable.');
        }

        if ($transfer['statut'] !== 'EN_ATTENTE') {
            throw new InvalidArgumentException('Ce transfert ne peut pas être exécuté à ce stade.');
        }

        if ((int)$transfer['id_agence_source'] === (int)$transfer['id_agence_destination']) {
            throw new InvalidArgumentException('L’agence source et l’agence destination doivent être différentes.');
        }

        if ((float)$transfer['montant'] <= 0) {
            throw new InvalidArgumentException('Le montant doit être supérieur à zéro.');
        }

        $this->beginTransaction();

        try {
            require_once APP_PATH . '/models/AgenceSoldeService.php';
            require_once APP_PATH . '/models/MouvementSoldeAgence.php';

            $soldeModel = new AgenceSoldeService();
            $mvtModel   = new MouvementSoldeAgence();

            $sourceSolde = $soldeModel->getOrCreate(
                (int)$transfer['id_agence_source'],
                (int)$transfer['id_service'],
                'FLOAT'
            );
            $destSolde = $soldeModel->getOrCreate(
                (int)$transfer['id_agence_destination'],
                (int)$transfer['id_service'],
                'FLOAT'
            );

            $montant = (float)$transfer['montant'];
            if ((float)$sourceSolde['montant_actuel'] < $montant) {
                throw new InvalidArgumentException('Le solde float de l’agence source est insuffisant.');
            }

            $sourceResult = $soldeModel->mettreAJour((int)$sourceSolde['id_solde'], $montant, 'DEBIT');
            $mvtModel->createMouvement(
                $idTransfert,
                (int)$sourceSolde['id_solde'],
                'DEBIT',
                $montant,
                $sourceResult['solde_avant'],
                $sourceResult['solde_apres'],
                'Transfert vers ' . $transfer['agence_destination']
            );

            $destResult = $soldeModel->mettreAJour((int)$destSolde['id_solde'], $montant, 'CREDIT');
            $mvtModel->createMouvement(
                $idTransfert,
                (int)$destSolde['id_solde'],
                'CREDIT',
                $montant,
                $destResult['solde_avant'],
                $destResult['solde_apres'],
                'Transfert depuis ' . $transfer['agence_source']
            );

            $this->update($idTransfert, [
                'statut'        => 'EXECUTEE',
                'id_valide_par' => $idValidePar,
            ]);

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
