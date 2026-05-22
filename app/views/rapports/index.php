<?php $moisLabels = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre']; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-file-earmark-text"></i> Rapports</h1>
        <p class="text-muted">Exporter et analyser les transactions.</p>
    </div>
    <a href="<?= url('rapports/export?mois=' . $mois . '&annee=' . $annee) ?>" class="btn btn-primary">
        <i class="bi bi-download"></i> Exporter CSV
    </a>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong><i class="bi bi-funnel"></i> Filtrer les rapports</strong>
            <div class="text-muted small">Choisissez la période à analyser.</div>
        </div>
    </div>
    <div class="card-body">
        <form method="get" action="<?= url('rapports') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Mois</label>
                <select name="mois" class="form-select">
                    <?php foreach ($moisLabels as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $key === (int)$mois ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Année</label>
                <input type="number" name="annee" class="form-control" value="<?= e($annee) ?>" min="2020">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filtrer</button>
            </div>
        </form>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong><i class="bi bi-table"></i> Résultats</strong>
            <div class="text-muted small">Transactions trouvées pour la période sélectionnée.</div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($transactions)): ?>
            <p class="mb-0">Aucune transaction trouvée pour cette période.</p>
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
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php if (!empty($benefices)): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong><i class="bi bi-graph-up"></i> Bénéfices par service</strong>
                <div class="text-muted small">Analyse des commissions et profits.</div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <h5 class="card-title mb-3">Bénéfices par service</h5>
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Catégorie</th>
                        <th>Commission totale</th>
                        <th>Bénéfice</th>
                        <th>Perte</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($benefices as $benefice): ?>
                        <tr>
                            <td><?= e($benefice['nom_service']) ?></td>
                            <td><?= e($benefice['categorie']) ?></td>
                            <td><?= e(formatMontant((float)$benefice['total_commission'])) ?></td>
                            <td><?= e(formatMontant((float)$benefice['total_benefice'])) ?></td>
                            <td><?= e(formatMontant((float)$benefice['total_perte'])) ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
