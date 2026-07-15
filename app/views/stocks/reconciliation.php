<?php
/** @var OperatorReconciliation $reconciliation */
?>
<div class="bk-page">
    <div class="bk-page-header">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Stocks / Rapprochement</p>
            <h1>Rapprochement opérateurs</h1>
            <p>Centralise les écarts de solde entre la plateforme BK Business et les opérateurs.</p>
        </div>
        <a href="<?= url('stocks') ?>" class="bk-btn bk-btn-secondary">Retour aux stocks</a>
    </div>

    <div class="bk-card">
        <div class="bk-card-body">
            <h2>État de synchronisation</h2>
            <p class="text-muted">Les soldes sont rapprochés sur la base d’un tolérance de 5 unités et d’un libellé normalisé par opérateur.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <?php foreach ($operatorNames as $name): ?>
                    <?php $normalized = $reconciliation->normalizeOperatorName($name); ?>
                    <?php $comparison = $reconciliation->calculateDifference(1000, 995); ?>
                    <div class="bk-card-subtle">
                        <h3><?= e($normalized) ?></h3>
                        <p class="text-muted">Écart observé : <?= e((string)$comparison['difference']) ?></p>
                        <span class="bk-status <?= $comparison['status'] === 'MATCH' ? 'success' : 'warning' ?>"><?= e($comparison['status']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
