<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Transactions / Détail</p>
            <h1>Transaction #<?= e($transaction['id_transaction']) ?></h1>
            <p>Détail de l'opération et mouvements de solde associés.</p>
        </div>
        <div class="bk-actions">
            <a href="<?= url('transactions') ?>" class="bk-btn bk-btn-secondary">
                <i data-lucide="arrow-left"></i> Retour
            </a>
            <?php if ($transaction['statut'] !== 'ANNULEE' && Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
                <a href="<?= url('transactions/' . $transaction['id_transaction'] . '/edit') ?>" class="bk-btn bk-btn-secondary">
                    <i data-lucide="edit-2"></i> Modifier
                </a>
                <form action="<?= url('transactions/' . $transaction['id_transaction'] . '/cancel') ?>" method="post">
                    <?= csrfField() ?>
                    <button type="submit" class="bk-btn bk-btn-danger">
                        <i data-lucide="x-circle"></i> Annuler
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <section class="bk-kpis">
        <article class="bk-kpi">
            <div class="bk-kpi-icon"><i data-lucide="radio-tower"></i></div>
            <p>Service</p>
            <strong><?= e($transaction['nom_service']) ?></strong>
            <span><?= e($transaction['libelle_type']) ?></span>
        </article>
        <article class="bk-kpi green">
            <div class="bk-kpi-icon"><i data-lucide="banknote"></i></div>
            <p>Montant</p>
            <strong><?= e(formatMontant((float)$transaction['montant'])) ?></strong>
            <span><?= e($transaction['reference'] ?: 'Sans référence') ?></span>
        </article>
        <article class="bk-kpi sky">
            <div class="bk-kpi-icon"><i data-lucide="user"></i></div>
            <p>Agent</p>
            <strong><?= e($transaction['nom_agent']) ?></strong>
            <span><?= e($transaction['nom_agence'] ?? $transaction['agence'] ?? '-') ?></span>
        </article>
        <article class="bk-kpi amber">
            <div class="bk-kpi-icon"><i data-lucide="calendar"></i></div>
            <p>Date</p>
            <strong><?= e(formatDate($transaction['date_heure'])) ?></strong>
            <span><?= e($transaction['statut']) ?></span>
        </article>
    </section>

    <section class="app-card">
        <div class="app-card-header"><span><i data-lucide="building-2"></i> Agence et nature</span></div>
        <div class="app-card-body">
            <div class="tx-summary">
                <div><span>Agence</span><strong><?= e($transaction['nom_agence'] ?? $transaction['agence'] ?? '—') ?></strong></div>
                <div><span>Motif</span><strong><?= e($transaction['motif_transaction'] ?: '—') ?></strong></div>
                <div><span>Nature transaction</span><strong><?= e($transaction['nature_transaction'] ?: '—') ?></strong></div>
                <div><span>Type mouvement</span><strong><?= e($transaction['type_mouvement'] ?: '—') ?></strong></div>
            </div>
        </div>
    </section>

    <?php if (!empty($transaction['nom_expediteur']) || !empty($transaction['nom_benefis'])): ?>
        <section class="tx-party-grid">
            <article class="app-card">
                <div class="app-card-header"><span><i data-lucide="user-round"></i> Expéditeur</span></div>
                <div class="app-card-body">
                    <div class="tx-summary">
                        <div><span>Nom complet</span><strong><?= e($transaction['nom_expediteur'] ?: '—') ?></strong></div>
                        <div><span>Numéro CNI</span><strong><?= e($transaction['expediteur_identifiant'] ?? '—') ?></strong></div>
                        <div><span>Téléphone</span><strong><?= e($transaction['expediteur_telephone'] ?? '—') ?></strong></div>
                    </div>
                </div>
            </article>

            <article class="app-card">
                <div class="app-card-header"><span><i data-lucide="user-check"></i> Bénéficiaire</span></div>
                <div class="app-card-body">
                    <div class="tx-summary">
                        <div><span>Nom complet</span><strong><?= e($transaction['nom_benefis'] ?: '—') ?></strong></div>
                        <div><span>Numéro CNI</span><strong><?= e($transaction['beneficiaire_identifiant'] ?? '—') ?></strong></div>
                        <div><span>Téléphone</span><strong><?= e($transaction['beneficiaire_telephone'] ?? '—') ?></strong></div>
                    </div>
                </div>
            </article>
        </section>
    <?php endif; ?>

    <section class="app-card">
        <div class="app-card-header"><span><i data-lucide="message-square"></i> Note</span></div>
        <div class="app-card-body">
            <p><?= nl2br(e($transaction['note'] ?: 'Aucune note.')) ?></p>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <span><i data-lucide="activity"></i> Mouvements de solde</span>
            <span class="app-badge app-badge-secondary"><?= e(count($mouvements)) ?> lignes</span>
        </div>
        <div class="app-card-body p-0">
            <?php if (empty($mouvements)): ?>
                <div class="bk-empty">
                    <i data-lucide="inbox"></i>
                    <strong>Aucun mouvement enregistré</strong>
                    <span>Cette transaction n'a pas généré d'historique de solde.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="app-table bk-table-min">
                        <thead>
                            <tr>
                                <th>Type solde</th>
                                <th>Nature</th>
                                <th class="text-right">Montant</th>
                                <th class="text-right">Solde avant</th>
                                <th class="text-right">Solde après</th>
                                <th>Date</th>
                                <th>Motif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mouvements as $mvt): ?>
                                <tr>
                                    <td><?= e($mvt['type_solde']) ?></td>
                                    <td><span class="bk-status neutral"><?= e($mvt['nature']) ?></span></td>
                                    <td class="text-right"><?= e(formatMontant((float)$mvt['montant'])) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$mvt['solde_avant'])) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$mvt['solde_apres'])) ?></td>
                                    <td><?= e(formatDate($mvt['date_heure'])) ?></td>
                                    <td><?= e($mvt['motif'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
