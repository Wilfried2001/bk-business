<?php
    $statuses = [
        'EN_ATTENTE' => 'En attente',
        'VALIDEE'    => 'Validée',
        'EXECUTEE'   => 'Exécutée',
        'REFUSEE'    => 'Refusée',
    ];
?>

<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Transferts / Float</p>
            <h1>Transferts de float</h1>
            <p>Liste des demandes de transfert de float entre agences.</p>
        </div>
        <?php if (Auth::hasRole(['AGENT', 'SUPERVISEUR', 'DG'])): ?>
            <div class="bk-actions">
                <a href="<?= url('transferts-float/create') ?>" class="bk-btn bk-btn-primary">
                    <i data-lucide="arrow-right-circle"></i> Nouvelle demande
                </a>
            </div>
        <?php endif; ?>
    </section>

    <section class="app-card">
        <div class="app-card-body p-0">
            <?php if (empty($transferts)): ?>
                <div class="bk-empty">
                    <i data-lucide="inbox"></i>
                    <strong>Aucune demande de transfert</strong>
                    <span>Créez une nouvelle demande pour échanger du float entre agences.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="app-table bk-table-min">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Source</th>
                                <th>Destination</th>
                                <th>Service</th>
                                <th class="text-right">Montant</th>
                                <th>Statut</th>
                                <th>Demandé par</th>
                                <th>Date</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transferts as $transfert): ?>
                                <tr>
                                    <td><?= e($transfert['id_transfert']) ?></td>
                                    <td><?= e($transfert['agence_source']) ?></td>
                                    <td><?= e($transfert['agence_destination']) ?></td>
                                    <td><?= e($transfert['nom_service']) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$transfert['montant'])) ?></td>
                                    <td><span class="bk-status <?= e($transfert['statut'] === 'EXECUTEE' ? 'success' : ($transfert['statut'] === 'REFUSEE' ? 'danger' : 'warning')) ?>"><?= e($statuses[$transfert['statut']] ?? $transfert['statut']) ?></span></td>
                                    <td><?= e($transfert['demandeur']) ?></td>
                                    <td><?= e(formatDate($transfert['created_at'])) ?></td>
                                    <td class="text-right">
                                        <a href="<?= url('transferts-float/' . $transfert['id_transfert']) ?>" class="bk-icon-btn" aria-label="Voir">
                                            <i data-lucide="eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
