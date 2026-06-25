<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="percent"></i> Commissions</h1>
        <p class="text-gray-500">Analyse des commissions par service.</p>
    </div>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header flex justify-between items-center">
        <div>
            <strong><i data-lucide="filter"></i> Filtrer les commissions</strong>
            <div class="text-gray-500 small">Sélectionnez une période pour afficher les résultats.</div>
        </div>
    </div>
    <div class="app-card-body">
        <form method="get" action="<?= url('commissions') ?>" class="flex flex-wrap -mx-3 gap-4 items-end">
            <div class="md:w-1/4 px-3">
                <label class="app-label"><i data-lucide="building"></i> Service</label>
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
                <label class="app-label"><i data-lucide="calendar"></i> Mois</label>
                <select name="mois" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                    <?php foreach ($moisLabels as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $key === (int)$mois ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:w-1/6 px-3">
                <label class="app-label"><i data-lucide="calendar"></i> Année</label>
                <input type="number" name="annee" class="border rounded-md px-3 py-2 text-sm w-full" value="<?= e($annee) ?>" min="2020">
            </div>
            <div class="md:w-1/6 px-3">
                <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white w-full">
                    <i data-lucide="filter"></i> Filtrer
                </button>
            </div>
            <div class="md:w-1/6 px-3">
                <a href="<?= url('commissions') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 w-full">
                    <i data-lucide="rotate-ccw"></i> Réinitialiser
                </a>
            </div>
            <div class="w-full px-3 text-right mt-2">
                <div class="fw-semibold text-success fs-5">
                    <i data-lucide="dollar-sign"></i> Total : <?= e(formatMontant((float)$total)) ?>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header">
        <i data-lucide="table"></i> Détails des commissions
    </div>
    <div class="app-card-body">
        <?php if (empty($benefices)): ?>
            <p class="mb-0">Aucune donnée de commission trouvée.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
            <table class="app-table table-mobile-details">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th class="hidden md:table-cell">Catégorie</th>
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
                            <td><span class="truncate" title="<?= e($benefice['nom_service']) ?>" data-toggle="tooltip"><?= e($benefice['nom_service']) ?></span></td>
                            <td class="hidden md:table-cell"><span class="truncate" title="<?= e($benefice['categorie']) ?>" data-toggle="tooltip"><?= e($benefice['categorie']) ?></span></td>
                            <td><span class="truncate" title="<?= e($benefice['nb_transactions']) ?>" data-toggle="tooltip"><?= e($benefice['nb_transactions']) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$benefice['total_commission'])) ?>" data-toggle="tooltip"><?= e(formatMontant((float)$benefice['total_commission'])) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$benefice['total_benefice'])) ?>" data-toggle="tooltip"><?= e(formatMontant((float)$benefice['total_benefice'])) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$benefice['total_perte'])) ?>" data-toggle="tooltip"><?= e(formatMontant((float)$benefice['total_perte'])) ?></span></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>
