<?php
    $moisLabels = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
    $totalMontant = 0;
    foreach ($transactions as $tx) {
        $totalMontant += (float)$tx['montant'];
    }
?>

<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Rapports</p>
            <h1>Rapports</h1>
            <p>Exporter et analyser les transactions sur une période donnée.</p>
        </div>
        <div class="bk-actions">
            <form method="get" action="<?= url('rapports/export') ?>" class="flex items-center gap-2">
                <input type="hidden" name="mois" value="<?= e($mois) ?>">
                <input type="hidden" name="annee" value="<?= e($annee) ?>">
                <input type="hidden" name="service" value="<?= e($filtres['id_service'] ?? '') ?>">
                <select name="format" class="bk-select">
                    <option value="csv">CSV</option>
                    <option value="pdf">PDF</option>
                    <option value="xlsx">Excel (.xls)</option>
                    <option value="json">JSON</option>
                    <option value="html">HTML</option>
                </select>
                <button type="submit" class="bk-btn bk-btn-primary">
                    <i data-lucide="download"></i> Exporter
                </button>
            </form>
        </div>
    </section>

    <section class="bk-report-types">
        <article class="bk-kpi">
            <div class="bk-kpi-icon"><i data-lucide="calendar-days"></i></div>
            <p>Rapport journalier</p>
            <span>Vue par jour</span>
        </article>
        <article class="bk-kpi sky">
            <div class="bk-kpi-icon"><i data-lucide="calendar-range"></i></div>
            <p>Rapport hebdomadaire</p>
            <span>Vue par semaine</span>
        </article>
        <article class="bk-kpi green">
            <div class="bk-kpi-icon"><i data-lucide="calendar"></i></div>
            <p>Rapport mensuel</p>
            <span>Vue par mois</span>
        </article>
        <article class="bk-kpi amber">
            <div class="bk-kpi-icon"><i data-lucide="archive"></i></div>
            <p>Rapport annuel</p>
            <span>Vue par année</span>
        </article>
    </section>

    <section class="bk-kpis">
        <article class="bk-kpi">
            <div class="bk-kpi-icon"><i data-lucide="receipt-text"></i></div>
            <p>Transactions trouvées</p>
            <strong><?= e(count($transactions)) ?></strong>
            <span>Période sélectionnée</span>
        </article>
        <article class="bk-kpi green">
            <div class="bk-kpi-icon"><i data-lucide="banknote"></i></div>
            <p>Volume total</p>
            <strong><?= e(formatMontant($totalMontant)) ?></strong>
            <span>Montants cumulés</span>
        </article>
        <article class="bk-kpi sky">
            <div class="bk-kpi-icon"><i data-lucide="badge-percent"></i></div>
            <p>Services</p>
            <strong><?= e(count($services)) ?></strong>
            <span>Disponibles</span>
        </article>
        <article class="bk-kpi amber">
            <div class="bk-kpi-icon"><i data-lucide="calendar-check"></i></div>
            <p>Période</p>
            <strong><?= e($moisLabels[(int)$mois] ?? $mois) ?></strong>
            <span><?= e($annee) ?></span>
        </article>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="settings-2"></i> Générer un rapport</span>
        </div>
        <div class="app-card-body">
            <form method="get" action="<?= url('rapports') ?>" class="bk-filter">
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
                <a href="<?= url('rapports') ?>" class="bk-btn bk-btn-secondary">
                    <i data-lucide="rotate-ccw"></i> Réinitialiser
                </a>
            </form>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="table"></i> Transactions du rapport</span>
            <span class="app-badge app-badge-secondary"><?= e(count($transactions)) ?> lignes</span>
        </div>
        <div class="app-card-body p-0">
            <?php if (empty($transactions)): ?>
                <div class="bk-empty">
                    <i data-lucide="inbox"></i>
                    <strong>Aucune transaction trouvée</strong>
                    <span>Ajustez les filtres pour générer un rapport.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="app-table table-mobile-details bk-table-min">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Type</th>
                                <th class="text-right">Montant</th>
                                <th class="hidden md:table-cell">Agent</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                                <tr>
                                    <td class="text-xs text-slate-500">#<?= e($tx['id_transaction']) ?></td>
                                    <td><?= e(formatDate($tx['date_heure'])) ?></td>
                                    <td class="font-semibold text-slate-800"><?= e($tx['nom_service']) ?></td>
                                    <td><?= e($tx['libelle_type']) ?></td>
                                    <td class="text-right font-semibold"><?= e(formatMontant((float)$tx['montant'])) ?></td>
                                    <td class="hidden md:table-cell"><?= e($tx['nom_agent']) ?></td>
                                    <td><span class="bk-status neutral"><?= e($tx['statut']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($benefices)): ?>
        <section class="app-card">
            <div class="app-card-header">
                <span><i data-lucide="trending-up"></i> Bénéfices par service</span>
            </div>
            <div class="app-card-body p-0">
                <div class="overflow-x-auto">
                    <table class="app-table bk-table-min">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Catégorie</th>
                                <th class="text-right">Commission</th>
                                <th class="text-right">Bénéfice</th>
                                <th class="text-right">Perte</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($benefices as $benefice): ?>
                                <tr>
                                    <td class="font-semibold text-slate-800"><?= e($benefice['nom_service']) ?></td>
                                    <td><?= e($benefice['categorie']) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$benefice['total_commission'])) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$benefice['total_benefice'])) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$benefice['total_perte'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>
