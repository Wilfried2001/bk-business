<?php
    $nbFloat = 0;
    $nbCaisse = 0;
    foreach ($alertes as $alerte) {
        if (($alerte['type_solde'] ?? '') === 'FLOAT') $nbFloat++;
        if (($alerte['type_solde'] ?? '') === 'CAISSE') $nbCaisse++;
    }
?>

<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Alertes</p>
            <h1>Alertes</h1>
            <p>Surveiller les seuils critiques et traiter les écarts de solde.</p>
        </div>
    </section>

    <section class="bk-kpis">
        <article class="bk-kpi <?= count($alertes) > 0 ? 'red' : 'green' ?>">
            <div class="bk-kpi-icon"><i data-lucide="siren"></i></div>
            <p>Toutes les alertes</p>
            <strong><?= e(count($alertes)) ?></strong>
            <span><?= e(count($alertes) > 0 ? 'À traiter' : 'Aucune anomalie') ?></span>
        </article>
        <article class="bk-kpi amber">
            <div class="bk-kpi-icon"><i data-lucide="wallet"></i></div>
            <p>Float</p>
            <strong><?= e($nbFloat) ?></strong>
            <span>Alertes Float</span>
        </article>
        <article class="bk-kpi sky">
            <div class="bk-kpi-icon"><i data-lucide="landmark"></i></div>
            <p>Caisse</p>
            <strong><?= e($nbCaisse) ?></strong>
            <span>Alertes Caisse</span>
        </article>
        <article class="bk-kpi green">
            <div class="bk-kpi-icon"><i data-lucide="shield-check"></i></div>
            <p>Résolues</p>
            <strong>0</strong>
            <span>Vue active seulement</span>
        </article>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="alert-circle"></i> Alertes actives</span>
            <span class="app-badge app-badge-secondary"><?= e(count($alertes)) ?> en cours</span>
        </div>
        <div class="app-card-body">
            <?php if (empty($alertes)): ?>
                <div class="bk-empty">
                    <i data-lucide="shield-check"></i>
                    <strong>Aucune alerte active</strong>
                    <span>Les soldes surveillés sont au-dessus de leurs seuils.</span>
                </div>
            <?php else: ?>
                <div class="alert-list">
                    <?php foreach ($alertes as $alerte): ?>
                        <div class="alert-card critique">
                            <div>
                                <strong><?= e($alerte['nom_service']) ?> — <?= e(ucfirst(strtolower($alerte['type_solde']))) ?></strong>
                                <span>Solde actuel: <?= e(formatMontant((float)$alerte['montant_actuel'])) ?> · Seuil: <?= e(formatMontant((float)$alerte['valeur_seuil'])) ?></span>
                                <small><?= e($alerte['message']) ?> · <?= e(formatDate($alerte['date_alerte'])) ?></small>
                            </div>
                            <form action="<?= url('alertes/' . $alerte['id_alerte'] . '/traiter') ?>" method="post">
                                <?= csrfField() ?>
                                <button type="submit" class="bk-btn bk-btn-danger">
                                    <i data-lucide="check"></i> Résoudre
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
