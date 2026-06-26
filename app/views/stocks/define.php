<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Stocks / Définir</p>
            <h1>Définir les stocks</h1>
            <p>Saisir les soldes Float et Caisse pour chaque service.</p>
        </div>
        <div class="bk-actions">
            <a href="<?= url('stocks') ?>" class="bk-btn bk-btn-secondary">
                <i data-lucide="arrow-left"></i> Retour
            </a>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="package"></i> Soldes initiaux</span>
        </div>
        <div class="app-card-body">
            <form action="<?= url('stocks/define') ?>" method="post" class="bk-page">
                <?= csrfField() ?>
                <div class="overflow-x-auto">
                    <table class="app-table bk-table-min">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Catégorie</th>
                                <th>Float</th>
                                <th>Caisse</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $s): ?>
                                <?php
                                    $float = null;
                                    $caisse = null;
                                    foreach ($s['soldes'] as $sd) {
                                        if ($sd['type_solde'] === 'FLOAT') $float = $sd;
                                        if ($sd['type_solde'] === 'CAISSE') $caisse = $sd;
                                    }
                                ?>
                                <tr>
                                    <td class="font-semibold text-slate-800"><?= e($s['nom']) ?></td>
                                    <td><?= e($s['categorie']) ?></td>
                                    <td>
                                        <?php if ($float): ?>
                                            <input type="number" step="0.01" name="montant[<?= e($float['id_solde']) ?>]" class="bk-field" value="<?= e($float['montant_actuel']) ?>">
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($caisse): ?>
                                            <input type="number" step="0.01" name="montant[<?= e($caisse['id_solde']) ?>]" class="bk-field" value="<?= e($caisse['montant_actuel']) ?>">
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div>
                    <label for="global_motif" class="bk-label">Motif des ajustements</label>
                    <textarea name="global_motif" id="global_motif" class="bk-field" rows="3" placeholder="Raison appliquée aux ajustements"></textarea>
                </div>

                <div class="bk-actions justify-end">
                    <a href="<?= url('stocks') ?>" class="bk-btn bk-btn-secondary">
                        <i data-lucide="x"></i> Annuler
                    </a>
                    <button type="submit" class="bk-btn bk-btn-primary">
                        <i data-lucide="save"></i> Enregistrer les stocks
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
