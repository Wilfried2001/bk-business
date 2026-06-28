<?php
$canManage = Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG']);
$summary = $summary ?? ['PRESENT' => 0, 'RETARD' => 0, 'ABSENT' => 0];
$selectedDate = $selectedDate ?? date('Y-m-d');
?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div>
        <p class="dashboard-eyebrow">Gestion RH</p>
        <h1 class="h3 mb-0"><i data-lucide="calendar-check-2"></i> Gestion des présences</h1>
        <p class="text-gray-500">Suivi des présences, absences et retards de la structure.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= url('presences/export?date=' . urlencode($selectedDate)) ?>" class="inline-flex items-center gap-2 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            <i data-lucide="download"></i> Export CSV
        </a>
        <a href="<?= url('dashboard') ?>" class="inline-flex items-center gap-2 rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i data-lucide="arrow-left"></i> Retour au tableau de bord
        </a>
    </div>
</div>

<div class="app-card shadow-sm mb-4">
    <div class="app-card-header">
        <strong><i data-lucide="calendar-days"></i> Filtrer la journée</strong>
    </div>
    <div class="app-card-body">
        <form method="get" action="<?= url('presences') ?>" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="date" class="app-label">Date</label>
                <input type="date" id="date" name="date" class="border rounded-md px-3 py-2 text-sm" value="<?= e($selectedDate) ?>">
            </div>
            <button type="submit" class="app-btn app-btn-primary">
                <i data-lucide="search"></i> Afficher
            </button>
        </form>
    </div>
</div>

<div class="grid gap-4 md:grid-cols-4 mb-4">
    <div class="app-card shadow-sm">
        <div class="app-card-body">
            <p class="text-sm text-gray-500">Employés suivis</p>
            <h3 class="text-2xl font-semibold"><?= (int)($stats['total_employes'] ?? 0) ?></h3>
        </div>
    </div>
    <div class="app-card shadow-sm">
        <div class="app-card-body">
            <p class="text-sm text-gray-500">Présents</p>
            <h3 class="text-2xl font-semibold"><?= (int)($stats['present'] ?? 0) ?></h3>
        </div>
    </div>
    <div class="app-card shadow-sm">
        <div class="app-card-body">
            <p class="text-sm text-gray-500">Retards</p>
            <h3 class="text-2xl font-semibold"><?= (int)($stats['retard'] ?? 0) ?></h3>
        </div>
    </div>
    <div class="app-card shadow-sm">
        <div class="app-card-body">
            <p class="text-sm text-gray-500">Absents</p>
            <h3 class="text-2xl font-semibold"><?= (int)($stats['absent'] ?? 0) ?></h3>
        </div>
    </div>
</div>

<div class="grid gap-4 lg:grid-cols-[1.2fr_0.8fr] mb-4">
    <div class="app-card shadow-sm">
        <div class="app-card-header"><strong><i data-lucide="trending-up"></i> Indicateurs RH</strong></div>
        <div class="app-card-body">
            <p class="text-sm text-gray-500">Taux de présence estimé sur la journée sélectionnée</p>
            <div class="mt-2 flex items-end gap-2">
                <span class="text-3xl font-semibold"><?= (int)($stats['taux_presence'] ?? 0) ?>%</span>
                <span class="text-sm text-gray-500">Présence + retard</span>
            </div>
            <p class="mt-3 text-sm text-gray-500">Les absences sont automatiquement enregistrées après la plage de présence définie.</p>
        </div>
    </div>
    <div class="app-card shadow-sm">
        <div class="app-card-header"><strong><i data-lucide="users-2"></i> Répartition par rôle</strong></div>
        <div class="app-card-body">
            <?php if (empty($roleStats)): ?>
                <p class="text-sm text-gray-500">Aucune donnée disponible.</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($roleStats as $roleStat): ?>
                        <div class="flex items-center justify-between rounded-md border border-slate-200 px-3 py-2 text-sm">
                            <span><?= e($roleStat['role']) ?></span>
                            <span class="text-gray-500">P: <?= (int)$roleStat['present'] ?> / R: <?= (int)$roleStat['retard'] ?> / A: <?= (int)$roleStat['absent'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="app-card shadow-sm">
    <div class="app-card-header">
        <strong><i data-lucide="users"></i> Présences de tous les employés</strong>
    </div>
    <div class="app-card-body">
        <?php if (empty($records)): ?>
            <div class="empty-state compact">
                <i data-lucide="inbox"></i>
                <span>Aucune donnée de présence pour cette journée.</span>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="app-table table-mobile-details">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Heure</th>
                            <th>Motif du retard</th>
                            <th>Commentaire</th>
                            <?php if ($canManage): ?><th>Action</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $record): ?>
                            <tr>
                                <td>
                                    <strong><?= e($record['nom']) ?></strong><br>
                                    <small class="text-gray-500"><?= e($record['email']) ?></small>
                                </td>
                                <td><?= e($record['role']) ?></td>
                                <td>
                                    <?php if (($record['statut'] ?? '') === 'RETARD'): ?>
                                        <span class="app-badge app-badge-warning"><i data-lucide="clock-3"></i> Retard</span>
                                    <?php elseif (($record['statut'] ?? '') === 'ABSENT'): ?>
                                        <span class="app-badge app-badge-secondary"><i data-lucide="user-x"></i> Absent</span>
                                    <?php else: ?>
                                        <span class="app-badge app-badge-success"><i data-lucide="check-circle"></i> Présent</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($record['heure_arrivee'] ?? '—') ?></td>
                                <td><?= e($record['motif_retard'] ?? '—') ?></td>
                                <td><?= e($record['commentaire'] ?? '—') ?></td>
                                <?php if ($canManage): ?>
                                <td>
                                    <form method="post" action="<?= url('presences/save') ?>" class="flex flex-wrap gap-2">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="user_id" value="<?= (int)$record['id_user'] ?>">
                                        <input type="hidden" name="date" value="<?= e($selectedDate) ?>">
                                        <select name="statut" class="border rounded-md px-2 py-1 text-sm">
                                            <option value="PRESENT" <?= (($record['statut'] ?? '') === 'PRESENT' ? 'selected' : '') ?>>Présent</option>
                                            <option value="RETARD" <?= (($record['statut'] ?? '') === 'RETARD' ? 'selected' : '') ?>>Retard</option>
                                            <option value="ABSENT" <?= (($record['statut'] ?? '') === 'ABSENT' ? 'selected' : '') ?>>Absent</option>
                                        </select>
                                        <button type="submit" class="app-btn app-btn-primary">Enregistrer</button>
                                    </form>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
