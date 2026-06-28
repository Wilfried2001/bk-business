<?php
class PresenceController extends Controller {
    public function index(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);

        require_once APP_PATH . '/models/Presence.php';
        require_once APP_PATH . '/models/Utilisateur.php';

        $presenceModel = new Presence();
        $userModel = new Utilisateur();
        $date = $this->get('date', date('Y-m-d'));
        $employees = $userModel->query(
            'SELECT id_user, nom, role, email FROM utilisateur WHERE actif = 1 ORDER BY nom ASC'
        );

        $records = [];
        foreach ($employees as $employee) {
            $status = $presenceModel->getStatusForDate((int)$employee['id_user'], $date);
            $records[] = [
                'id_user' => (int)$employee['id_user'],
                'nom' => $employee['nom'],
                'role' => $employee['role'],
                'email' => $employee['email'],
                'statut' => $status['statut'] ?? null,
                'motif_retard' => $status['motif_retard'] ?? null,
                'heure_arrivee' => $status['heure_arrivee'] ?? null,
                'commentaire' => $status['commentaire'] ?? null,
            ];
        }

        $summary = $presenceModel->getSummaryForDate($date);
        $stats = [
            'total_employes' => count($employees),
            'present' => (int)($summary['PRESENT'] ?? 0),
            'retard' => (int)($summary['RETARD'] ?? 0),
            'absent' => (int)($summary['ABSENT'] ?? 0),
        ];
        $stats['taux_presence'] = $stats['total_employes'] > 0
            ? round((($stats['present'] + $stats['retard']) / $stats['total_employes']) * 100)
            : 0;

        $roleStats = [];
        foreach ($records as $record) {
            $role = $record['role'] ?? 'AUTRE';
            if (!isset($roleStats[$role])) {
                $roleStats[$role] = [
                    'role' => $role,
                    'present' => 0,
                    'retard' => 0,
                    'absent' => 0,
                ];
            }
            $status = $record['statut'] ?? 'PRESENT';
            if ($status === 'RETARD') {
                $roleStats[$role]['retard']++;
            } elseif ($status === 'ABSENT') {
                $roleStats[$role]['absent']++;
            } else {
                $roleStats[$role]['present']++;
            }
        }
        usort($roleStats, function ($a, $b) {
            return strcmp($a['role'], $b['role']);
        });

        $this->render('presences/index', [
            'pageTitle' => 'Présences',
            'records' => $records,
            'summary' => $summary,
            'stats' => $stats,
            'roleStats' => $roleStats,
            'selectedDate' => $date,
        ], 'Gestion des présences');
    }

    public function save(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
        $this->verifyCsrf();

        require_once APP_PATH . '/models/Presence.php';
        $presenceModel = new Presence();

        $userId = (int)$this->post('user_id', 0);
        $date = $this->post('date', date('Y-m-d'));
        $statut = $this->post('statut', 'PRESENT');
        $motifRetard = $this->post('motif_retard', null);
        $commentaire = $this->post('commentaire', null);
        $heureArrivee = $this->post('heure_arrivee', null);

        if ($userId <= 0) {
            Session::flash('error', 'Utilisateur introuvable.');
            $this->redirect('presences');
        }

        $presenceModel->saveForUser($userId, $statut, $motifRetard, $commentaire, $date, $heureArrivee);
        Session::flash('success', 'Présence mise à jour.');
        $this->redirect('presences?date=' . urlencode($date));
    }

    public function saveLateReason(): void {
        Auth::requireAuth();
        $this->verifyCsrf();

        require_once APP_PATH . '/models/Presence.php';
        $presenceModel = new Presence();

        $userId = (int)$this->post('user_id', 0);
        $motifRetard = $this->post('motif_retard', '');
        $commentaire = $this->post('commentaire', '');
        $date = $this->post('date', date('Y-m-d'));

        if ($userId <= 0) {
            $this->json(['success' => false, 'error' => 'Utilisateur introuvable.']);
        }

        $presenceModel->saveForUser($userId, 'RETARD', $motifRetard, $commentaire, $date, date('H:i:s'));
        Session::remove('presence_late');
        Session::flash('success', 'Motif de retard enregistré avec succès.');
        $this->redirect('dashboard');
    }

    public function export(): void {
        Auth::requireRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);

        require_once APP_PATH . '/models/Presence.php';
        require_once APP_PATH . '/models/Utilisateur.php';

        $presenceModel = new Presence();
        $userModel = new Utilisateur();
        $date = $this->get('date', date('Y-m-d'));
        $employees = $userModel->query(
            'SELECT id_user, nom, role, email FROM utilisateur WHERE actif = 1 ORDER BY nom ASC'
        );

        $rows = [];
        foreach ($employees as $employee) {
            $status = $presenceModel->getStatusForDate((int)$employee['id_user'], $date);
            $rows[] = [
                'employe' => $employee['nom'],
                'role' => $employee['role'],
                'email' => $employee['email'],
                'statut' => $status['statut'] ?? 'ABSENT',
                'heure_arrivee' => $status['heure_arrivee'] ?? '—',
                'motif_retard' => $status['motif_retard'] ?? '—',
                'commentaire' => $status['commentaire'] ?? '—',
            ];
        }

        $filename = 'presences_' . $date . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Employé', 'Rôle', 'Email', 'Statut', 'Heure d\'arrivée', 'Motif du retard', 'Commentaire']);
        foreach ($rows as $row) {
            fputcsv($output, [
                $row['employe'],
                $row['role'],
                $row['email'],
                $row['statut'],
                $row['heure_arrivee'],
                $row['motif_retard'],
                $row['commentaire'],
            ]);
        }
        fclose($output);
        exit;
    }
}
