<?php
    $servicesGroupes = [];
    foreach ($soldes as $row) {
        $sid = $row['id_service'];
        if (!isset($servicesGroupes[$sid])) {
            $servicesGroupes[$sid] = [
                'id_service' => $sid,
                'nom_service' => $row['nom_service'],
                'categorie' => $row['categorie'],
                'float' => null,
                'caisse' => null,
            ];
        }
        if ($row['type_solde'] === 'FLOAT') {
            $servicesGroupes[$sid]['float'] = $row;
        } else {
            $servicesGroupes[$sid]['caisse'] = $row;
        }
    }

    $totalFloat = 0;
    $totalCaisse = 0;
    $nbAlertes = 0;
    foreach ($servicesGroupes as $service) {
        if ($service['float']) {
            $totalFloat += (float)$service['float']['montant_actuel'];
            if (!empty($service['float']['en_alerte'])) $nbAlertes++;
        }
        if ($service['caisse']) {
            $totalCaisse += (float)$service['caisse']['montant_actuel'];
            if (!empty($service['caisse']['en_alerte'])) $nbAlertes++;
        }
    }
?>

<script nonce="<?= e(cspNonce()) ?>">
    window.baseUrl = <?= json_encode(BASE_URL) ?>;
    window.canEditSeuil = <?= json_encode(Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])) ?>;
    window.csrfToken = <?= json_encode(generateCsrf()) ?>;
</script>

<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Stocks & Soldes</p>
            <h1>Stocks & Soldes</h1>
            <p>Suivi consolidé des soldes Float et Caisse par service.</p>
        </div>
        <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
            <div class="bk-actions">
                <a href="<?= url('stocks/define') ?>" class="bk-btn bk-btn-primary">
                    <i data-lucide="package-plus"></i> Ajouter un solde
                </a>
                <a href="<?= url('stocks/seuils/all') ?>" class="bk-btn bk-btn-secondary">
                    <i data-lucide="sliders-horizontal"></i> Configurer seuils
                </a>
                <a href="<?= url('stocks/reconciliation') ?>" class="bk-btn bk-btn-primary">
                    <i data-lucide="refresh-cw"></i> Rapprochement opérateurs
                </a>
            </div>
        <?php endif; ?>
    </section>

    <section class="bk-kpis">
        <article class="bk-kpi">
            <div class="bk-kpi-icon"><i data-lucide="radio-tower"></i></div>
            <p>Services surveillés</p>
            <strong><?= e(count($servicesGroupes)) ?></strong>
            <span>Actifs</span>
        </article>
        <article class="bk-kpi green">
            <div class="bk-kpi-icon"><i data-lucide="wallet"></i></div>
            <p>Soldes Float</p>
            <strong><?= e(formatMontant($totalFloat)) ?></strong>
            <span>Total disponible</span>
        </article>
        <article class="bk-kpi sky">
            <div class="bk-kpi-icon"><i data-lucide="landmark"></i></div>
            <p>Soldes Caisse</p>
            <strong><?= e(formatMontant($totalCaisse)) ?></strong>
            <span>Total caisse</span>
        </article>
        <article class="bk-kpi <?= $nbAlertes > 0 ? 'red' : 'green' ?>">
            <div class="bk-kpi-icon"><i data-lucide="siren"></i></div>
            <p>Alertes actives</p>
            <strong><?= e($nbAlertes) ?></strong>
            <span><?= e($nbAlertes > 0 ? 'Action requise' : 'Situation stable') ?></span>
        </article>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="list"></i> Soldes par service</span>
            <span class="app-badge app-badge-secondary"><?= e(count($servicesGroupes)) ?> services</span>
        </div>
        <div class="app-card-body p-0">
            <?php if (empty($servicesGroupes)): ?>
                <div class="bk-empty">
                    <i data-lucide="inbox"></i>
                    <strong>Aucun solde disponible</strong>
                    <span>Définissez les premiers soldes pour démarrer le suivi.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="app-table table-mobile-details bk-table-min">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th class="hidden md:table-cell">Catégorie</th>
                                <th class="text-right">Float</th>
                                <th class="text-right">Seuil Float</th>
                                <th class="text-right">Caisse</th>
                                <th class="text-right">Seuil Caisse</th>
                                <th>État</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicesGroupes as $service): ?>
                                <?php
                                    $float = $service['float'];
                                    $caisse = $service['caisse'];
                                    $alerte = ($float && !empty($float['en_alerte'])) || ($caisse && !empty($caisse['en_alerte']));
                                ?>
                                <tr>
                                    <td class="font-semibold text-slate-800"><?= e($service['nom_service']) ?></td>
                                    <td class="hidden md:table-cell"><?= e($service['categorie']) ?></td>
                                    <td class="text-right"><?= $float ? e(formatMontant((float)$float['montant_actuel'])) : '—' ?></td>
                                    <td class="text-right"><?= $float && $float['valeur_seuil'] !== null ? e(formatMontant((float)$float['valeur_seuil'])) : 'N/A' ?></td>
                                    <td class="text-right"><?= $caisse ? e(formatMontant((float)$caisse['montant_actuel'])) : '—' ?></td>
                                    <td class="text-right"><?= $caisse && $caisse['valeur_seuil'] !== null ? e(formatMontant((float)$caisse['valeur_seuil'])) : 'N/A' ?></td>
                                    <td><span class="bk-status <?= e($alerte ? 'danger' : 'success') ?>"><?= e($alerte ? 'Alerte' : 'OK') ?></span></td>
                                    <td class="text-right">
                                        <a href="<?= url('stocks/' . $service['id_service']) ?>" class="bk-icon-btn" aria-label="Voir les détails">
                                            <i data-lucide="eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
