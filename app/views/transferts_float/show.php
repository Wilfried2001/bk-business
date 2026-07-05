<?php
    $statuses = [
        'EN_ATTENTE' => 'En attente',
        'VALIDEE'    => 'Validée',
        'EXECUTEE'   => 'Exécutée',
        'REFUSEE'    => 'Refusée',
    ];
?>

<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Transferts / Float</p>
            <h1>Transfert #<?= e($transfert['id_transfert']) ?></h1>
            <p>Détail de la demande de transfert de float entre agences.</p>
        </div>
        <div class="bk-actions">
            <a href="<?= url('transferts-float') ?>" class="bk-btn bk-btn-secondary">
                <i data-lucide="arrow-left"></i> Retour
            </a>
            <?php if (Auth::hasRole(['SUPERVISEUR', 'DG']) && $transfert['statut'] === 'EN_ATTENTE'): ?>
                <form action="<?= url('transferts-float/' . $transfert['id_transfert'] . '/approve') ?>" method="post" class="inline-block">
                    <?= csrfField() ?>
                    <button type="submit" class="bk-btn bk-btn-success">
                        <i data-lucide="check-circle"></i> Valider et exécuter
                    </button>
                </form>
                <button type="button" class="bk-btn bk-btn-danger" id="refuseTransferBtn">
                    <i data-lucide="x-circle"></i> Refuser
                </button>
            <?php endif; ?>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-body">
            <div class="tx-summary">
                <div><span>Agence source</span><strong><?= e($transfert['agence_source']) ?></strong></div>
                <div><span>Agence destination</span><strong><?= e($transfert['agence_destination']) ?></strong></div>
                <div><span>Service</span><strong><?= e($transfert['nom_service']) ?></strong></div>
                <div><span>Montant</span><strong><?= e(formatMontant((float)$transfert['montant'])) ?></strong></div>
                <div><span>Motif</span><strong><?= e($transfert['motif'] ?: '—') ?></strong></div>
                <div><span>Demandé par</span><strong><?= e($transfert['demandeur']) ?></strong></div>
                <div><span>Statut</span><strong><?= e($statuses[$transfert['statut']] ?? $transfert['statut']) ?></strong></div>
                <?php if (!empty($transfert['valideur'])): ?>
                    <div><span>Validé par</span><strong><?= e($transfert['valideur']) ?></strong></div>
                <?php endif; ?>
                <?php if (!empty($transfert['commentaire'])): ?>
                    <div><span>Commentaire</span><strong><?= e($transfert['commentaire']) ?></strong></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-header"><span><i data-lucide="activity"></i> Historique des mouvements</span></div>
        <div class="app-card-body p-0">
            <?php if (empty($mouvements)): ?>
                <div class="bk-empty">
                    <i data-lucide="inbox"></i>
                    <strong>Aucun mouvement enregistré</strong>
                    <span>Le transfert n'a pas encore été exécuté.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="app-table bk-table-min">
                        <thead>
                            <tr>
                                <th>Agence</th>
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
                                    <td><?= e($mvt['nom_agence']) ?></td>
                                    <td><?= e($mvt['type_solde']) ?></td>
                                    <td><?= e($mvt['nature']) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$mvt['montant'])) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$mvt['solde_avant'])) ?></td>
                                    <td class="text-right"><?= e(formatMontant((float)$mvt['solde_apres'])) ?></td>
                                    <td><?= e(formatDate($mvt['date_heure'])) ?></td>
                                    <td><?= e($mvt['motif'] ?: '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php if (Auth::hasRole(['SUPERVISEUR', 'DG']) && $transfert['statut'] === 'EN_ATTENTE'): ?>
<form id="rejectForm" action="<?= url('transferts-float/' . $transfert['id_transfert'] . '/reject') ?>" method="post" class="hidden">
    <?= csrfField() ?>
    <input type="hidden" name="commentaire" id="rejectCommentaire">
</form>

<div class="modal" id="rejectModal" hidden>
    <div class="modal-content">
        <h2>Refuser le transfert</h2>
        <label for="rejectReason">Commentaire</label>
        <textarea id="rejectReason" class="bk-field" rows="4"></textarea>
        <div class="bk-actions justify-end mt-4">
            <button type="button" class="bk-btn bk-btn-secondary" id="cancelReject">Annuler</button>
            <button type="button" class="bk-btn bk-btn-danger" id="confirmReject">Confirmer le refus</button>
        </div>
    </div>
</div>

<script>
    document.getElementById('refuseTransferBtn').addEventListener('click', function () {
        document.getElementById('rejectModal').hidden = false;
    });
    document.getElementById('cancelReject').addEventListener('click', function () {
        document.getElementById('rejectModal').hidden = true;
    });
    document.getElementById('confirmReject').addEventListener('click', function () {
        var reason = document.getElementById('rejectReason').value.trim();
        if (!reason) {
            alert('Veuillez saisir un commentaire de refus.');
            return;
        }
        document.getElementById('rejectCommentaire').value = reason;
        document.getElementById('rejectForm').submit();
    });
</script>
<?php endif; ?>
