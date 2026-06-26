<?php
    $totalMontant = 0;
    $nbAnnulees = 0;
    $nbValidees = 0;
    $nbEnCours = 0;

    foreach ($transactions as $tx) {
        $totalMontant += (float)$tx['montant'];
        if (($tx['statut'] ?? '') === 'ANNULEE') $nbAnnulees++;
        if (($tx['statut'] ?? '') === 'VALIDEE') $nbValidees++;
        if (($tx['statut'] ?? '') === 'EN_COURS') $nbEnCours++;
    }

    $statusLabel = function (string $status): string {
        return match ($status) {
            'VALIDEE' => 'Validée',
            'EN_COURS' => 'En cours',
            'ANNULEE' => 'Annulée',
            default => ucfirst(strtolower(str_replace('_', ' ', $status))),
        };
    };

    $statusClass = function (string $status): string {
        return match ($status) {
            'VALIDEE' => 'success',
            'EN_COURS' => 'warning',
            'ANNULEE' => 'danger',
            default => 'secondary',
        };
    };
?>

<style>
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }

    .transactions-view .app-btn-brand {
        background-image: linear-gradient(to right, #5b4bff, #6d5dfc);
        color: #fff;
        box-shadow: 0 10px 22px rgba(91, 75, 255, .22);
    }

    .transactions-view .app-btn-brand:hover {
        background-image: linear-gradient(to right, #4f46e5, #5b4bff);
        color: #fff;
    }

    .transactions-view .dashboard-head {
        margin-bottom: 1rem;
    }

    .transactions-view .transaction-filter-card .app-card-body {
        padding: 1rem;
    }

    .transactions-view .transaction-filter-form {
        display: grid;
        grid-template-columns: 1.5fr repeat(5, minmax(8.5rem, 1fr)) auto;
        gap: .75rem;
        align-items: center;
    }

    .transactions-view .transaction-filter-form .app-field {
        height: 2.75rem;
        border-color: #e7eaf1;
        background-color: #fff;
        font-size: .8125rem;
    }

    .transactions-view .filter-search {
        min-width: 15rem;
    }

    .transactions-view .app-field-icon {
        position: relative;
    }

    .transactions-view .app-field-icon svg {
        position: absolute;
        left: .75rem;
        top: 50%;
        height: 1rem;
        width: 1rem;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .transactions-view .app-field-icon .app-field {
        padding-left: 2.25rem;
    }

    .transactions-view .filter-actions {
        display: flex;
        gap: .5rem;
        white-space: nowrap;
    }

    .transactions-view .filter-actions .app-btn {
        height: 2.75rem;
    }

    .transactions-view .transaction-kpi-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
    }

    .transactions-view .transaction-table-card .app-card-header {
        padding: .95rem 1.1rem;
    }

    .transactions-view .transaction-table {
        min-width: 62rem;
    }

    .transactions-view .transaction-table thead {
        background-color: #fbfcff;
    }

    .transactions-view .status-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: .25rem .55rem;
        font-size: .75rem;
        line-height: 1rem;
        font-weight: 700;
    }

    .transactions-view .status-success {
        background-color: #dcfce7;
        color: #15803d;
    }

    .transactions-view .status-warning {
        background-color: #fef3c7;
        color: #b45309;
    }

    .transactions-view .status-danger {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .transactions-view .status-secondary {
        background-color: #f1f5f9;
        color: #475569;
    }

    .transactions-view .icon-action {
        display: inline-flex;
        height: 2rem;
        width: 2rem;
        align-items: center;
        justify-content: center;
        border-radius: .375rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        transition: color .15s ease, background-color .15s ease, border-color .15s ease;
    }

    .transactions-view .icon-action svg {
        height: 1rem;
        width: 1rem;
    }

    .transactions-view .icon-action:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #0f172a;
    }

    .transactions-view .icon-action-primary {
        color: #5b4bff;
        border-color: rgba(91, 75, 255, .3);
    }

    .transactions-view .icon-action-primary:hover {
        background: #f1efff;
        border-color: rgba(91, 75, 255, .45);
        color: #4f46e5;
    }

    .transactions-view .icon-action-danger {
        color: #dc2626;
        border-color: rgba(220, 38, 38, .25);
    }

    .transactions-view .icon-action-danger:hover {
        background: #fef2f2;
        border-color: rgba(220, 38, 38, .42);
        color: #b91c1c;
    }

    .transactions-view .empty-state {
        display: flex;
        min-height: 14rem;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: 2rem;
        text-align: center;
        color: #64748b;
    }

    .transactions-view .empty-state svg {
        height: 2.75rem;
        width: 2.75rem;
        color: #cbd5e1;
    }

    .transactions-view .empty-state strong {
        font-size: 1rem;
        color: #0f172a;
    }

    .transactions-view .empty-state span {
        font-size: .875rem;
    }

    @media (min-width: 768px) {
        .transactions-view .transaction-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .transactions-view .transaction-kpi-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 1279px) {
        .transactions-view .transaction-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .transactions-view .filter-search,
        .transactions-view .filter-actions {
            grid-column: 1 / -1;
        }

        .transactions-view .filter-actions .app-btn {
            flex: 1;
        }
    }

    @media (max-width: 640px) {
        .transactions-view .transaction-filter-form {
            grid-template-columns: 1fr;
        }

        .transactions-view .filter-actions {
            flex-direction: column;
        }

        .transactions-view .dashboard-actions,
        .transactions-view .dashboard-actions .app-btn {
            width: 100%;
        }
    }
</style>

<div class="business-dashboard transactions-view">
    <section class="dashboard-head">
        <div>
            <p class="dashboard-eyebrow">Tableau de bord / Transactions</p>
            <h1>Transactions</h1>
            <p>Filtrer, contrôler et consulter l'historique des opérations.</p>
        </div>
        <div class="dashboard-actions">
            <a href="<?= url('transactions/create') ?>" class="app-btn app-btn-brand">
                <i data-lucide="plus"></i> Nouvelle transaction
            </a>
        </div>
    </section>

    <section class="transaction-filter-card app-card">
        <div class="app-card-body">
            <form method="get" action="<?= url('transactions') ?>" class="transaction-filter-form">
                <div class="filter-search">
                    <label class="sr-only" for="search">Recherche</label>
                    <div class="app-field-icon">
                        <i data-lucide="search"></i>
                        <input id="search" type="search" name="search" class="app-field" placeholder="Rechercher une transaction..." value="<?= e($filtres['search'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="sr-only" for="service">Service</label>
                    <select id="service" name="service" class="app-field">
                        <option value="">Tous les services</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= e($service['id_service']) ?>" <?= $service['id_service'] == ($filtres['id_service'] ?? '') ? 'selected' : '' ?>>
                                <?= e($service['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="sr-only" for="type">Type</label>
                    <select id="type" name="type" class="app-field">
                        <option value="">Tous les types</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= e($type['id_type']) ?>" <?= $type['id_type'] == ($filtres['id_type'] ?? '') ? 'selected' : '' ?>>
                                <?= e($type['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="sr-only" for="statut">Statut</label>
                    <select id="statut" name="statut" class="app-field">
                        <option value="">Tous les statuts</option>
                        <?php foreach ($statuts as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $value === ($filtres['statut'] ?? '') ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="sr-only" for="date_debut">Date début</label>
                    <input id="date_debut" type="date" name="date_debut" class="app-field" value="<?= e($filtres['date_debut'] ?? '') ?>">
                </div>

                <div>
                    <label class="sr-only" for="date_fin">Date fin</label>
                    <input id="date_fin" type="date" name="date_fin" class="app-field" value="<?= e($filtres['date_fin'] ?? '') ?>">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="app-btn app-btn-brand">
                        <i data-lucide="filter"></i> Filtrer
                    </button>
                    <a href="<?= url('transactions') ?>" class="app-btn app-btn-secondary">
                        <i data-lucide="rotate-ccw"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="transaction-kpi-grid">
        <article class="business-kpi-card accent-transactions">
            <div class="kpi-icon"><i data-lucide="receipt-text"></i></div>
            <p>Total transactions</p>
            <strong><?= e(count($transactions)) ?></strong>
            <span><?= e($nbValidees) ?> validées</span>
        </article>

        <article class="business-kpi-card accent-volume">
            <div class="kpi-icon"><i data-lucide="banknote"></i></div>
            <p>Volume total</p>
            <strong><?= e(formatMontant($totalMontant)) ?></strong>
            <span>Transactions filtrées</span>
        </article>

        <article class="business-kpi-card accent-commission">
            <div class="kpi-icon"><i data-lucide="loader-circle"></i></div>
            <p>En cours</p>
            <strong><?= e($nbEnCours) ?></strong>
            <span>À suivre</span>
        </article>

        <article class="business-kpi-card accent-alert">
            <div class="kpi-icon"><i data-lucide="ban"></i></div>
            <p>Transactions annulées</p>
            <strong><?= e($nbAnnulees) ?></strong>
            <span class="money-trend <?= $nbAnnulees > 0 ? 'down' : 'neutral' ?>"><?= e($nbAnnulees > 0 ? 'À vérifier' : 'Aucune annulation') ?></span>
        </article>
    </section>

    <section class="app-card transaction-table-card">
        <div class="app-card-header">
            <span><i data-lucide="list"></i> Historique des transactions</span>
            <span class="app-badge app-badge-secondary"><?= e(count($transactions)) ?> lignes</span>
        </div>

        <div class="app-card-body p-0">
            <?php if (empty($transactions)): ?>
                <div class="empty-state">
                    <i data-lucide="inbox"></i>
                    <strong>Aucune transaction trouvée</strong>
                    <span>Ajustez les filtres ou créez une nouvelle transaction.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="app-table table-mobile-details transaction-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Type</th>
                                <th class="text-right">Montant</th>
                                <th class="hidden md:table-cell">Agent</th>
                                <th>Statut</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                                <?php
                                    $currentStatus = (string)($tx['statut'] ?? '');
                                    $badgeClass = $statusClass($currentStatus);
                                ?>
                                <tr>
                                    <td class="text-xs text-slate-500">
                                        #<?= e($tx['id_transaction']) ?>
                                    </td>
                                    <td>
                                        <span class="truncate" title="<?= e(formatDate($tx['date_heure'])) ?>" data-toggle="tooltip"><?= e(formatDate($tx['date_heure'])) ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-slate-800">
                                            <span class="truncate" title="<?= e($tx['nom_service']) ?>" data-toggle="tooltip"><?= e($tx['nom_service']) ?></span>
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="truncate" title="<?= e($tx['libelle_type']) ?>" data-toggle="tooltip"><?= e($tx['libelle_type']) ?></span>
                                    </td>
                                    <td class="text-right font-semibold text-slate-900">
                                        <span class="truncate" title="<?= e(formatMontant((float)$tx['montant'])) ?>" data-toggle="tooltip"><?= e(formatMontant((float)$tx['montant'])) ?></span>
                                    </td>
                                    <td class="hidden md:table-cell">
                                        <span class="truncate" title="<?= e($tx['nom_agent']) ?>" data-toggle="tooltip"><?= e($tx['nom_agent']) ?></span>
                                    </td>
                                    <td>
                                        <span class="status-pill status-<?= e($badgeClass) ?>"><?= e($statusLabel($currentStatus)) ?></span>
                                    </td>
                                    <td class="text-right">
                                        <div class="table-actions justify-end">
                                            <a href="<?= url('transactions/' . $tx['id_transaction']) ?>" class="icon-action" aria-label="Voir la transaction">
                                                <i data-lucide="eye"></i>
                                            </a>
                                            <?php if ($currentStatus !== 'ANNULEE' && Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
                                                <a href="<?= url('transactions/' . $tx['id_transaction'] . '/edit') ?>" class="icon-action icon-action-primary" aria-label="Modifier la transaction">
                                                    <i data-lucide="edit-2"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($currentStatus !== 'ANNULEE' && Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
                                                <form action="<?= url('transactions/' . $tx['id_transaction'] . '/cancel') ?>" method="post" class="inline-block">
                                                    <?= csrfField() ?>
                                                    <button type="submit" class="icon-action icon-action-danger" aria-label="Annuler la transaction">
                                                        <i data-lucide="x-circle"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
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
