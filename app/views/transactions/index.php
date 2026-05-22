<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-arrow-left-right"></i> Transactions</h1>
        <p class="text-muted">Filtrer et consulter l'historique des transactions.</p>
    </div>
    <a href="<?= url('transactions/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouvelle transaction
    </a>
</div>
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= url('transactions') ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Service</label>
                <select name="service" class="form-select">
                    <option value="">Tous</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= e($service['id_service']) ?>" <?= $service['id_service'] == ($filtres['id_service'] ?? '') ? 'selected' : '' ?>>
                            <?= e($service['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date début</label>
                <input type="date" name="date_debut" class="form-control" value="<?= e($filtres['date_debut'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date fin</label>
                <input type="date" name="date_fin" class="form-control" value="<?= e($filtres['date_fin'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">Filtrer</button>
            </div>
        </form>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Historique des transactions
    </div>
    <div class="card-body table-responsive">
        <?php if (empty($transactions)): ?>
            <p class="mb-0">Aucune transaction trouvée.</p>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-mobile-details">
                <thead>
                    <tr>
                        <th class="d-none d-md-table-cell">#</th>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th class="d-none d-md-table-cell">Agent</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td class="d-none d-md-table-cell"><span class="truncate" title="<?= e($tx['id_transaction']) ?>" data-bs-toggle="tooltip"><?= e($tx['id_transaction']) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatDate($tx['date_heure'])) ?>" data-bs-toggle="tooltip"><?= e(formatDate($tx['date_heure'])) ?></span></td>
                            <td><span class="truncate" title="<?= e($tx['nom_service']) ?>" data-bs-toggle="tooltip"><?= e($tx['nom_service']) ?></span></td>
                            <td><span class="truncate" title="<?= e($tx['libelle_type']) ?>" data-bs-toggle="tooltip"><?= e($tx['libelle_type']) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$tx['montant'])) ?>" data-bs-toggle="tooltip"><?= e(formatMontant((float)$tx['montant'])) ?></span></td>
                            <td class="d-none d-md-table-cell"><span class="truncate" title="<?= e($tx['nom_agent']) ?>" data-bs-toggle="tooltip"><?= e($tx['nom_agent']) ?></span></td>
                            <td><span class="truncate" title="<?= e($tx['statut']) ?>" data-bs-toggle="tooltip"><?= e($tx['statut']) ?></span></td>
                            <td class="text-end">
                                <a href="<?= url('transactions/' . $tx['id_transaction']) ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                                <?php if ($tx['statut'] !== 'ANNULEE' && Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
                                    <form action="<?= url('transactions/' . $tx['id_transaction'] . '/cancel') ?>" method="post" class="d-inline-block ms-1">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x-circle"></i> Annuler
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>
