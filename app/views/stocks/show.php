<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Stocks / Détail</p>
            <h1>Stock — <?= e($service['nom']) ?></h1>
            <p><?= e($service['categorie']) ?></p>
        </div>
        <div class="bk-actions">
            <a href="<?= url('stocks') ?>" class="bk-btn bk-btn-secondary">
                <i data-lucide="arrow-left"></i> Retour
            </a>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="wallet-cards"></i> Soldes du service</span>
            <span class="app-badge app-badge-secondary"><?= e(count($soldes)) ?> soldes</span>
        </div>
        <div class="app-card-body p-0">
            <div class="overflow-x-auto">
                <table class="app-table bk-table-min">
                    <thead>
                        <tr>
                            <th>Type solde</th>
                            <th class="text-right">Montant</th>
                            <th>Date de mise à jour</th>
                            <th class="text-right">Seuil</th>
                            <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                                <th class="text-right">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($soldes as $solde): ?>
                            <tr>
                                <td><span class="bk-status <?= $solde['type_solde'] === 'FLOAT' ? 'info' : 'warning' ?>"><?= e(ucfirst(strtolower($solde['type_solde']))) ?></span></td>
                                <td class="text-right font-semibold"><?= e(formatMontant((float)$solde['montant_actuel'])) ?></td>
                                <td><?= e(formatDate($solde['date_maj'])) ?></td>
                                <td class="text-right"><?= $solde['valeur_seuil'] !== null ? e(formatMontant((float)$solde['valeur_seuil'])) : '—' ?></td>
                                <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                                    <td>
                                        <div class="bk-page" style="gap:.5rem;">
                                            <form action="<?= url('stocks/' . $service['id_service'] . '/seuil') ?>" method="post" class="bk-actions justify-end">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="id_solde" value="<?= e($solde['id_solde']) ?>">
                                                <input type="hidden" name="redirect_to" value="stocks/<?= e($service['id_service']) ?>">
                                                <input type="number" name="valeur_seuil" class="bk-field" step="0.01" value="<?= $solde['valeur_seuil'] !== null ? e($solde['valeur_seuil']) : '' ?>" placeholder="Seuil" required style="max-width:10rem;">
                                                <button type="submit" class="bk-btn bk-btn-primary">
                                                    <i data-lucide="sliders-horizontal"></i> Seuil
                                                </button>
                                            </form>

                                            <form action="<?= url('stocks/' . $service['id_service'] . '/solde') ?>" method="post" class="bk-actions justify-end">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="id_solde" value="<?= e($solde['id_solde']) ?>">
                                                <input type="number" name="montant_actuel" class="bk-field" step="0.01" value="<?= e($solde['montant_actuel']) ?>" placeholder="Montant" required style="max-width:10rem;">
                                                <input type="text" name="motif" class="bk-field" placeholder="Motif" style="max-width:14rem;">
                                                <button type="submit" class="bk-btn bk-btn-secondary">
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
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="history"></i> Historique des seuils</span>
            <span class="app-badge app-badge-secondary"><?= e(count($seuilHistories)) ?> lignes</span>
        </div>
        <div class="app-card-body p-0">
            <?php if (empty($seuilHistories)): ?>
                <div class="bk-empty">
                    <i data-lucide="inbox"></i>
                    <strong>Aucun historique</strong>
                    <span>Aucune modification de seuil enregistrée pour ce service.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="app-table bk-table-min">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type solde</th>
                                <th class="text-right">Ancien seuil</th>
                                <th class="text-right">Nouveau seuil</th>
                                <th>Modifié par</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seuilHistories as $history): ?>
                                <tr>
                                    <td><?= e(formatDate($history['date_modification'], 'd/m/Y H:i')) ?></td>
                                    <td><?= e($history['type_solde']) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$history['ancienne_valeur'])) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$history['nouvelle_valeur'])) ?></td>
                                    <td><?= e($history['modifie_par_nom'] ?? 'Système') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
