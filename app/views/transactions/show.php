<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-receipt"></i> Transaction #<?= e($transaction['id_transaction']) ?></h1>
        <p class="text-muted">Détail de la transaction.</p>
    </div>
    <?php if ($transaction['statut'] !== 'ANNULEE' && Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
        <div class="d-flex gap-2">
            <a href="<?= url('transactions/' . $transaction['id_transaction'] . '/edit') ?>" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i> Modifier
            </a>
            <form action="<?= url('transactions/' . $transaction['id_transaction'] . '/cancel') ?>" method="post">
                <?= csrfField() ?>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> Annuler
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>
<div class="row gy-4">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Service</dt>
                    <dd class="col-sm-7"><?= e($transaction['nom_service']) ?></dd>
                    <dt class="col-sm-5">Type</dt>
                    <dd class="col-sm-7"><?= e($transaction['libelle_type']) ?></dd>
                    <dt class="col-sm-5">Montant</dt>
                    <dd class="col-sm-7"><?= e(formatMontant((float)$transaction['montant'])) ?></dd>
                    <dt class="col-sm-5">Agent</dt>
                    <dd class="col-sm-7"><?= e($transaction['nom_agent']) ?></dd>
                    <dt class="col-sm-5">Date</dt>
                    <dd class="col-sm-7"><?= e(formatDate($transaction['date_heure'])) ?></dd>
                    <dt class="col-sm-5">Statut</dt>
                    <dd class="col-sm-7"><?= e($transaction['statut']) ?></dd>
                    <dt class="col-sm-5">Référence</dt>
                    <dd class="col-sm-7"><?= e($transaction['reference'] ?: '-') ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-chat-left-text"></i> Note
            </div>
            <div class="card-body">
                <p class="mb-0"><?= nl2br(e($transaction['note'] ?: 'Aucune note.')) ?></p>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <i class="bi bi-activity"></i> Mouvements de solde
    </div>
    <div class="card-body">
        <?php if (empty($mouvements)): ?>
            <p class="mb-0">Aucun mouvement enregistré.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Type solde</th>
                            <th>Nature</th>
                            <th>Montant</th>
                            <th>Solde avant</th>
                            <th>Solde après</th>
                            <th>Date</th>
                            <th>Motif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mouvements as $mvt): ?>
                            <tr>
                                <td><?= e($mvt['type_solde']) ?></td>
                                <td><?= e($mvt['nature']) ?></td>
                                <td><?= e(formatMontant((float)$mvt['montant'])) ?></td>
                                <td><?= e(formatMontant((float)$mvt['solde_avant'])) ?></td>
                                <td><?= e(formatMontant((float)$mvt['solde_apres'])) ?></td>
                                <td><?= e(formatDate($mvt['date_heure'])) ?></td>
                                <td><?= e($mvt['motif'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
