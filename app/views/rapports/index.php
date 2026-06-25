<?php $moisLabels = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre']; ?>
<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="file-text"></i> Rapports</h1>
        <p class="text-gray-500">Exporter et analyser les transactions.</p>
    </div>
    <a href="<?= url('rapports/export?mois=' . $mois . '&annee=' . $annee . '&service=' . ($filtres['id_service'] ?? '')) ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
        <i data-lucide="download"></i> Exporter CSV
    </a>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header flex justify-between items-center">
        <div>
            <strong><i data-lucide="filter"></i> Filtrer les rapports</strong>
            <div class="text-gray-500 small">Choisissez la période à analyser.</div>
        </div>
    </div>
    <div class="app-card-body">
        <form method="get" action="<?= url('rapports') ?>" class="flex flex-wrap -mx-3 gap-4 items-end">
            <div class="md:w-1/4 px-3">
                <label class="app-label">Service</label>
                <select name="service" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                    <option value="">Tous</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= e($service['id_service']) ?>" <?= $service['id_service'] === ($filtres['id_service'] ?? 0) ? 'selected' : '' ?>>
                            <?= e($service['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:w-1/4 px-3">
                <label class="app-label">Mois</label>
                <select name="mois" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                    <?php foreach ($moisLabels as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $key === (int)$mois ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:w-1/6 px-3">
                <label class="app-label">Année</label>
                <input type="number" name="annee" class="border rounded-md px-3 py-2 text-sm w-full" value="<?= e($annee) ?>" min="2020">
            </div>
            <div class="md:w-1/6 px-3">
                <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white w-full">
                    <i data-lucide="filter"></i> Filtrer
                </button>
            </div>
            <div class="md:w-1/6 px-3">
                <a href="<?= url('rapports') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 w-full">
                    <i data-lucide="rotate-ccw"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header flex justify-between items-center">
        <div>
            <strong><i data-lucide="table"></i> Résultats</strong>
            <div class="text-gray-500 small">Transactions trouvées pour la période sélectionnée.</div>
        </div>
    </div>
    <div class="app-card-body">
        <?php if (empty($transactions)): ?>
            <p class="mb-0">Aucune transaction trouvée pour cette période.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
            <table class="app-table table-mobile-details">
                <thead>
                    <tr>
                        <th class="hidden md:table-cell">#</th>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th class="hidden md:table-cell">Agent</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td class="hidden md:table-cell"><span class="truncate" title="<?= e($tx['id_transaction']) ?>" data-toggle="tooltip"><?= e($tx['id_transaction']) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatDate($tx['date_heure'])) ?>" data-toggle="tooltip"><?= e(formatDate($tx['date_heure'])) ?></span></td>
                            <td><span class="truncate" title="<?= e($tx['nom_service']) ?>" data-toggle="tooltip"><?= e($tx['nom_service']) ?></span></td>
                            <td><span class="truncate" title="<?= e($tx['libelle_type']) ?>" data-toggle="tooltip"><?= e($tx['libelle_type']) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$tx['montant'])) ?>" data-toggle="tooltip"><?= e(formatMontant((float)$tx['montant'])) ?></span></td>
                            <td class="hidden md:table-cell"><span class="truncate" title="<?= e($tx['nom_agent']) ?>" data-toggle="tooltip"><?= e($tx['nom_agent']) ?></span></td>
                            <td><span class="truncate" title="<?= e($tx['statut']) ?>" data-toggle="tooltip"><?= e($tx['statut']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php if (!empty($benefices)): ?>
    <div class="app-card shadow-sm mb-4">
        <div class="app-card-header flex justify-between items-center">
            <div>
                <strong><i data-lucide="trending-up"></i> Bénéfices par service</strong>
                <div class="text-gray-500 small">Analyse des commissions et profits.</div>
            </div>
        </div>
        <div class="app-card-body overflow-x-auto">
            <h5 class="text-base font-semibold text-slate-900 mb-3">Bénéfices par service</h5>
            <table class="app-table">
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
