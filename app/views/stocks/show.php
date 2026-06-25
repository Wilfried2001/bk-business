<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="package"></i> Stock — <?= e($service['nom']) ?></h1>
        <p class="text-gray-500"><?= e($service['categorie']) ?></p>
    </div>
    <a href="<?= url('stocks') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
        <i data-lucide="arrow-left"></i> Retour
    </a>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header flex justify-between items-center">
        <div>
            <strong><i data-lucide="list"></i> Historique des soldes</strong>
            <div class="text-gray-500 small">Évolution du solde pour ce service.</div>
        </div>
    </div>
    <div class="app-card-body overflow-x-auto">
        <table class="app-table">
            <thead>
                <tr>
                    <th>Type solde</th>
                    <th>Montant</th>
                    <th>Date de mise à jour</th>
                    <th>Seuil</th>
                    <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                        <th></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($soldes as $solde): ?>
                    <tr>
                        <td><?= e($solde['type_solde']) ?></td>
                        <td><?= e(formatMontant((float)$solde['montant_actuel'])) ?></td>
                        <td><?= e(formatDate($solde['date_maj'])) ?></td>
                        <td><?= $solde['valeur_seuil'] !== null ? e(formatMontant((float)$solde['valeur_seuil'])) : '—' ?></td>
                        <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                            <td>
                                <div class="flex gap-2 flex-col sm:flex-row items-center justify-end">
                                    <form action="<?= url('stocks/' . $service['id_service'] . '/seuil') ?>" method="post" class="flex gap-2 items-center">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_solde" value="<?= e($solde['id_solde']) ?>">
                                        <input type="hidden" name="redirect_to" value="stocks/<?= e($service['id_service']) ?>">
                                        <input type="number" name="valeur_seuil" class="border rounded-md px-3 py-2 text-sm w-full px-2 py-1 text-xs" step="0.01" value="<?= $solde['valeur_seuil'] !== null ? e($solde['valeur_seuil']) : '' ?>" placeholder="Seuil" required>
                                        <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
                                            <i data-lucide="sliders-horizontal"></i> Seuil
                                        </button>
                                    </form>

                                    <form action="<?= url('stocks/' . $service['id_service'] . '/solde') ?>" method="post" class="flex gap-2 items-center">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_solde" value="<?= e($solde['id_solde']) ?>">
                                        <input type="number" name="montant_actuel" class="border rounded-md px-3 py-2 text-sm w-full px-2 py-1 text-xs" step="0.01" value="<?= e($solde['montant_actuel']) ?>" placeholder="Montant" required>
                                        <input type="text" name="motif" class="border rounded-md px-3 py-2 text-sm w-full px-2 py-1 text-xs" placeholder="Motif (optionnel)" style="min-width:180px">
                                        <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs app-btn-secondary">
                                            <i data-lucide="save"></i> Mettre à jour
                                        </button>
                                    </form>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header">
        <i data-lucide="history"></i> Historique des seuils
    </div>
    <div class="app-card-body">
        <?php if (empty($seuilHistories)): ?>
            <p class="mb-0">Aucun historique de seuil enregistré pour ce service.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type solde</th>
                            <th>Ancien seuil</th>
                            <th>Nouveau seuil</th>
                            <th>Modifié par</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($seuilHistories as $history): ?>
                            <tr>
                                <td><?= e(formatDate($history['date_modification'], 'd/m/Y H:i')) ?></td>
                                <td><?= e($history['type_solde']) ?></td>
                                <td><?= e(formatMontant((float)$history['ancienne_valeur'])) ?></td>
                                <td><?= e(formatMontant((float)$history['nouvelle_valeur'])) ?></td>
                                <td><?= e($history['modifie_par_nom'] ?? 'Système') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
