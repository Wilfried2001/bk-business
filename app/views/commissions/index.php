<?php
    $nbTransactions = 0;
    $totalBenefice = 0;
    $totalPerte = 0;
    foreach ($benefices as $benefice) {
        $nbTransactions += (int)$benefice['nb_transactions'];
        $totalBenefice += (float)$benefice['total_benefice'];
        $totalPerte += (float)$benefice['total_perte'];
    }
?>

<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Commissions</p>
            <h1>Commissions</h1>
            <p>Analyser les commissions, bénéfices et pertes par service.</p>
        </div>
    </section>

    <section class="bk-kpis">
        <article class="bk-kpi">
            <div class="bk-kpi-icon"><i data-lucide="badge-percent"></i></div>
            <p>Commissions totales</p>
            <strong><?= e(formatMontant((float)$total)) ?></strong>
            <span>Période filtrée</span>
        </article>
        <article class="bk-kpi green">
            <div class="bk-kpi-icon"><i data-lucide="trending-up"></i></div>
            <p>Bénéfices</p>
            <strong><?= e(formatMontant($totalBenefice)) ?></strong>
            <span>Résultat positif</span>
        </article>
        <article class="bk-kpi red">
            <div class="bk-kpi-icon"><i data-lucide="trending-down"></i></div>
            <p>Pertes</p>
            <strong><?= e(formatMontant($totalPerte)) ?></strong>
            <span>Résultat négatif</span>
        </article>
        <article class="bk-kpi sky">
            <div class="bk-kpi-icon"><i data-lucide="receipt-text"></i></div>
            <p>Transactions rémunérées</p>
            <strong><?= e($nbTransactions) ?></strong>
            <span>Opérations</span>
        </article>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="filter"></i> Filtres</span>
        </div>
        <div class="app-card-body">
            <form method="get" action="<?= url('commissions') ?>" class="bk-filter">
                <div>
                    <label class="bk-label">Service</label>
                    <select name="service" class="bk-select">
                        <option value="">Tous les services</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= e($service['id_service']) ?>" <?= $service['id_service'] === ($filtres['id_service'] ?? 0) ? 'selected' : '' ?>>
                                <?= e($service['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="bk-label">Mois</label>
                    <select name="mois" class="bk-select">
                        <?php foreach ($moisLabels as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= $key === (int)$mois ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="bk-label">Année</label>
                    <input type="number" name="annee" class="bk-field" value="<?= e($annee) ?>" min="2020">
                </div>
                <button type="submit" class="bk-btn bk-btn-primary">
                    <i data-lucide="filter"></i> Filtrer
                </button>
                <a href="<?= url('commissions') ?>" class="bk-btn bk-btn-secondary">
                    <i data-lucide="rotate-ccw"></i> Réinitialiser
                </a>
            </form>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="table"></i> Commissions par service</span>
            <span class="app-badge app-badge-secondary"><?= e(count($benefices)) ?> lignes</span>
        </div>
        <div class="app-card-body p-0">
            <?php if (empty($benefices)): ?>
                <div class="bk-empty">
                    <i data-lucide="inbox"></i>
                    <strong>Aucune commission trouvée</strong>
                    <span>Changez la période ou le service pour élargir la recherche.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="app-table table-mobile-details bk-table-min">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th class="hidden md:table-cell">Catégorie</th>
                                <th class="text-right">Transactions</th>
                                <th class="text-right">Commission</th>
                                <th class="text-right">Bénéfice</th>
                                <th class="text-right">Perte</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($benefices as $benefice): ?>
                                <tr>
                                    <td class="font-semibold text-slate-800"><?= e($benefice['nom_service']) ?></td>
                                    <td class="hidden md:table-cell"><?= e($benefice['categorie']) ?></td>
                                    <td class="text-right"><?= e($benefice['nb_transactions']) ?></td>
                                    <td class="text-right font-semibold"><?= e(formatMontant((float)$benefice['total_commission'])) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$benefice['total_benefice'])) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$benefice['total_perte'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
