<?php
// ============================================================
//  app/models/Presence.php — Gestion des présences et retards
// ============================================================

class Presence extends Model {
    protected string $table = 'presence_employe';
    protected string $primaryKey = 'id_presence';

    public function hasStatusForDate(int $userId, ?string $date = null): bool {
        $date = $date ?? date('Y-m-d');
        $entry = $this->queryOne(
            'SELECT id_presence FROM presence_employe WHERE id_user = ? AND date_presence = ?',
            [$userId, $date]
        );
        return !empty($entry);
    }

    public function getStatusForDate(int $userId, ?string $date = null): ?array {
        $date = $date ?? date('Y-m-d');
        return $this->queryOne(
            'SELECT * FROM presence_employe WHERE id_user = ? AND date_presence = ?',
            [$userId, $date]
        );
    }

    public function isLateForUser(int $userId, ?string $date = null): bool {
        $date = $date ?? date('Y-m-d');
        if ($this->hasStatusForDate($userId, $date)) {
            return false;
        }

        $now = new DateTime('now');
        $lateThreshold = new DateTime($date . ' 08:30:00');
        return $now > $lateThreshold;
    }

    public function saveForUser(int $userId, string $statut, ?string $motifRetard, ?string $commentaire, ?string $date = null, ?string $heureArrivee = null): bool {
        $date = $date ?? date('Y-m-d');
        $entry = $this->getStatusForDate($userId, $date);

        $payload = [
            'id_user' => $userId,
            'date_presence' => $date,
            'statut' => $statut,
            'motif_retard' => $motifRetard ?: null,
            'commentaire' => $commentaire ?: null,
            'heure_arrivee' => $heureArrivee ?: null,
        ];

        if ($entry) {
            return $this->update((int)$entry['id_presence'], $payload);
        }

        return $this->create($payload) > 0;
    }

    public function getAllByDate(?string $date = null): array {
        $date = $date ?? date('Y-m-d');
        return $this->query(
            'SELECT pe.*, u.nom, u.role, u.email
             FROM presence_employe pe
             JOIN utilisateur u ON u.id_user = pe.id_user
             WHERE pe.date_presence = ?
             ORDER BY u.nom ASC',
            [$date]
        );
    }

    public function getSummaryForDate(?string $date = null): array {
        $date = $date ?? date('Y-m-d');
        $rows = $this->query(
            'SELECT statut, COUNT(*) AS total
             FROM presence_employe
             WHERE date_presence = ?
             GROUP BY statut',
            [$date]
        );

        $summary = [
            'PRESENT' => 0,
            'RETARD' => 0,
            'ABSENT' => 0,
        ];

        foreach ($rows as $row) {
            $statut = $row['statut'] ?? '';
            if (isset($summary[$statut])) {
                $summary[$statut] = (int)$row['total'];
            }
        }

        return $summary;
    }
}
