<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Stocks / Seuils</p>
            <h1>Seuils d'alerte</h1>
            <p>Configurer les seuils minimums pour les soldes Float et Caisse.</p>
        </div>
        <div class="bk-actions">
            <a href="<?= url('stocks') ?>" class="bk-btn bk-btn-secondary">
                <i data-lucide="arrow-left"></i> Retour
            </a>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="alert-triangle"></i> Gestion des seuils</span>
        </div>
        <div class="app-card-body">
            <?php if (empty($services)): ?>
                <div class="bk-empty">
                    <i data-lucide="inbox"></i>
                    <strong>Aucun service disponible</strong>
                    <span>Ajoutez des services avant de configurer les seuils.</span>
                </div>
            <?php else: ?>
                <form action="<?= url('stocks/seuils/save') ?>" method="post" class="bk-page">
                    <?= csrfField() ?>

                    <div class="overflow-x-auto">
                        <table class="app-table bk-table-min">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Catégorie</th>
                                    <th>Type</th>
                                    <th class="text-right">Solde actuel</th>
                                    <th class="text-right">Seuil actuel</th>
                                    <th>Nouveau seuil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                    <?php if (!empty($service['soldes'])): ?>
                                        <?php foreach ($service['soldes'] as $solde): ?>
                                            <tr>
                                                <td class="font-semibold text-slate-800"><?= e($service['nom']) ?></td>
                                                <td><?= e($service['categorie']) ?></td>
                                                <td>
                                                    <span class="bk-status <?= $solde['type_solde'] === 'FLOAT' ? 'info' : 'warning' ?>">
                                                        <?= e(ucfirst(strtolower($solde['type_solde']))) ?>
                                                    </span>
                                                </td>
                                                <td class="text-right"><?= e(formatMontant((float)$solde['montant_actuel'])) ?></td>
                                                <td class="text-right"><?= $solde['valeur_seuil'] !== null ? e(formatMontant((float)$solde['valeur_seuil'])) : '—' ?></td>
                                                <td>
                                                    <input type="number" name="seuil[<?= e($solde['id_solde']) ?>]" class="bk-field" step="0.01" min="0" value="<?= $solde['valeur_seuil'] !== null ? e($solde['valeur_seuil']) : '' ?>" placeholder="Entrez un seuil">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-gray-500 text-center">Aucun solde défini pour ce service</td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="bk-actions justify-end">
                        <a href="<?= url('stocks') ?>" class="bk-btn bk-btn-secondary">
                            <i data-lucide="x"></i> Annuler
                        </a>
                        <button type="submit" class="bk-btn bk-btn-primary">
                            <i data-lucide="check-circle"></i> Enregistrer tous les seuils
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <div class="app-alert app-alert-info app-alert-dismissible" role="alert">
        <strong><i data-lucide="info"></i> Information</strong>
        <p class="mb-0 mt-2">Le seuil d'alerte définit la valeur minimale en dessous de laquelle une alerte sera générée.</p>
        <button type="button" class="app-close" data-dismiss="alert" aria-label="Fermer"></button>
    </div>
</div>
