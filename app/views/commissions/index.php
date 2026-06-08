<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-percent"></i> Commissions</h1>
        <p class="text-muted">Analyse des commissions par service.</p>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong><i class="bi bi-funnel"></i> Filtrer les commissions</strong>
            <div class="text-muted small">Sélectionnez une période pour afficher les résultats.</div>
        </div>
    </div>
    <div class="card-body">
        <form method="get" action="<?= url('commissions') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label"><i class="bi bi-building"></i> Service</label>
                <select name="service" class="form-select">
                    <option value="">Tous</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= e($service['id_service']) ?>" <?= $service['id_service'] === ($filtres['id_service'] ?? 0) ? 'selected' : '' ?>>
                            <?= e($service['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="bi bi-calendar3"></i> Mois</label>
                <select name="mois" class="form-select">
                    <?php foreach ($moisLabels as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $key === (int)$mois ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label"><i class="bi bi-calendar2"></i> Année</label>
                <input type="number" name="annee" class="form-control" value="<?= e($annee) ?>" min="2020">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>
            </div>
            <div class="col-md-2">
                <a href="<?= url('commissions') ?>" class="btn btn-outline-secondary w-100">Réinitialiser</a>
            </div>
            <div class="col-md-12 text-end mt-2">
                <div class="fw-semibold text-success fs-5">
                    <i class="bi bi-cash-coin"></i> Total : <?= e(formatMontant((float)$total)) ?>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <i class="bi bi-table"></i> Détails des commissions
    </div>
    <div class="card-body">
        <?php if (empty($benefices)): ?>
            <p class="mb-0">Aucune donnée de commission trouvée.</p>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-mobile-details">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th class="d-none d-md-table-cell">Catégorie</th>
                        <th>Transactions</th>
                        <th>Commission totale</th>
                        <th>Bénéfice</th>
                        <th>Perte</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($benefices as $benefice): ?>
                        <tr>
                            <td><span class="truncate" title="<?= e($benefice['nom_service']) ?>" data-bs-toggle="tooltip"><?= e($benefice['nom_service']) ?></span></td>
                            <td class="d-none d-md-table-cell"><span class="truncate" title="<?= e($benefice['categorie']) ?>" data-bs-toggle="tooltip"><?= e($benefice['categorie']) ?></span></td>
                            <td><span class="truncate" title="<?= e($benefice['nb_transactions']) ?>" data-bs-toggle="tooltip"><?= e($benefice['nb_transactions']) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$benefice['total_commission'])) ?>" data-bs-toggle="tooltip"><?= e(formatMontant((float)$benefice['total_commission'])) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$benefice['total_benefice'])) ?>" data-bs-toggle="tooltip"><?= e(formatMontant((float)$benefice['total_benefice'])) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$benefice['total_perte'])) ?>" data-bs-toggle="tooltip"><?= e(formatMontant((float)$benefice['total_perte'])) ?></span></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>
