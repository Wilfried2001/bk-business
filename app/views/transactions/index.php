<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="arrow-left-right"></i> Transactions</h1>
        <p class="text-gray-500">Filtrer et consulter l'historique des transactions.</p>
    </div>
    <a href="<?= url('transactions/create') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
        <i data-lucide="plus-circle"></i> Nouvelle transaction
    </a>
</div>
<div class="app-card mb-4">
    <div class="app-card-body">
        <form method="get" action="<?= url('transactions') ?>" class="flex flex-wrap -mx-3 gap-4">
            <div class="md:w-1/4 px-3">
                <label class="app-label">Service</label>
                <select name="service" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                    <option value="">Tous</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= e($service['id_service']) ?>" <?= $service['id_service'] == ($filtres['id_service'] ?? '') ? 'selected' : '' ?>>
                            <?= e($service['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:w-1/4 px-3">
                <label class="app-label">Type</label>
                <select name="type" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                    <option value="">Tous</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= e($type['id_type']) ?>" <?= $type['id_type'] == ($filtres['id_type'] ?? '') ? 'selected' : '' ?>>
                            <?= e($type['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:w-1/6 px-3">
                <label class="app-label">Statut</label>
                <select name="statut" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                    <option value="">Tous</option>
                    <?php foreach ($statuts as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $value === ($filtres['statut'] ?? '') ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:w-1/3 px-3">
                <label class="app-label">Recherche</label>
                <input type="search" name="search" class="border rounded-md px-3 py-2 text-sm w-full" placeholder="Référence, agent, service ou type" value="<?= e($filtres['search'] ?? '') ?>">
            </div>
            <div class="md:w-1/6 px-3">
                <label class="app-label">Date début</label>
                <input type="date" name="date_debut" class="border rounded-md px-3 py-2 text-sm w-full" value="<?= e($filtres['date_debut'] ?? '') ?>">
            </div>
            <div class="md:w-1/6 px-3">
                <label class="app-label">Date fin</label>
                <input type="date" name="date_fin" class="border rounded-md px-3 py-2 text-sm w-full" value="<?= e($filtres['date_fin'] ?? '') ?>">
            </div>
            <div class="md:w-1/3 px-3 flex items-end gap-2">
                <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white w-full">
                    <i data-lucide="filter"></i> Filtrer
                </button>
                <a href="<?= url('transactions') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 w-full">
                    <i data-lucide="rotate-ccw"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>
<div class="flex flex-wrap -mx-3 mb-4">
    <div class="md:w-1/2 px-3">
        <div class="app-card border-start border-4 border-primary shadow-sm h-100">
            <div class="app-card-body">
                <h5 class="text-base font-semibold text-slate-900">Transactions</h5>
                <p class="text-sm text-slate-500 mb-0">Nombre de transactions affichées</p>
                <strong class="fs-3"><?= e(count($transactions)) ?></strong>
            </div>
        </div>
    </div>
    <div class="md:w-1/2 px-3">
        <div class="app-card border-start border-4 border-success shadow-sm h-100">
            <div class="app-card-body">
                <h5 class="text-base font-semibold text-slate-900">Montant total</h5>
                <p class="text-sm text-slate-500 mb-0">Total brut des transactions filtrées</p>
                <strong class="fs-3"><?php $totalMontant = 0; foreach ($transactions as $tx) { $totalMontant += (float)$tx['montant']; } echo e(formatMontant($totalMontant)); ?></strong>
            </div>
        </div>
    </div>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header">
        <i data-lucide="list"></i> Historique des transactions
    </div>
    <div class="app-card-body overflow-x-auto">
        <?php if (empty($transactions)): ?>
            <p class="mb-0">Aucune transaction trouvée.</p>
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
                            <td>
                                <?php
                                    $statusClass = 'secondary';
                                    if ($tx['statut'] === 'VALIDEE') $statusClass = 'success';
                                    if ($tx['statut'] === 'EN_COURS') $statusClass = 'warning';
                                    if ($tx['statut'] === 'ANNULEE') $statusClass = 'danger';
                                ?>
                                <span class="app-badge app-badge-<?= e($statusClass) ?>"><?= e($tx['statut']) ?></span>
                            </td>
                            <td class="text-right">
                                <a href="<?= url('transactions/' . $tx['id_transaction']) ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
                                    <i data-lucide="eye"></i> Voir
                                </a>
                                <?php if ($tx['statut'] !== 'ANNULEE' && Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
                                    <a href="<?= url('transactions/' . $tx['id_transaction'] . '/edit') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-[#0d47a1] text-[#0d47a1] bg-transparent hover:bg-[#eef4ff] ms-1">
                                        <i data-lucide="edit-2"></i> Modifier
                                    </a>
                                <?php endif; ?>
                                <?php if ($tx['statut'] !== 'ANNULEE' && Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
                                    <form action="<?= url('transactions/' . $tx['id_transaction'] . '/cancel') ?>" method="post" class="inline-block ms-1">
                                        <?= csrfField() ?>
                                        <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs bg-red-600 text-white">
                                            <i data-lucide="x-circle"></i> Annuler
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
