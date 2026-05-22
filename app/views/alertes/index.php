<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-exclamation-triangle"></i> Alertes</h1>
        <p class="text-muted">Aperçu des alertes de solde actives.</p>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong><i class="bi bi-exclamation-circle"></i> Alerte(s) actives</strong>
            <div class="text-muted small">Liste des alertes en cours et actions disponibles.</div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($alertes)): ?>
            <p class="mb-0">Aucune alerte active.</p>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-mobile-details">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Solde</th>
                        <th>Montant actuel</th>
                        <th>Seuil</th>
                        <th>Message</th>
                        <th class="d-none d-md-table-cell">Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alertes as $alerte): ?>
                        <tr>
                            <td><span class="truncate" title="<?= e($alerte['nom_service']) ?>" data-bs-toggle="tooltip"><?= e($alerte['nom_service']) ?></span></td>
                            <td><span class="truncate" title="<?= e($alerte['type_solde']) ?>" data-bs-toggle="tooltip"><?= e($alerte['type_solde']) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$alerte['montant_actuel'])) ?>" data-bs-toggle="tooltip"><?= e(formatMontant((float)$alerte['montant_actuel'])) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$alerte['valeur_seuil'])) ?>" data-bs-toggle="tooltip"><?= e(formatMontant((float)$alerte['valeur_seuil'])) ?></span></td>
                            <td><span class="truncate" title="<?= e($alerte['message']) ?>" data-bs-toggle="tooltip"><?= e($alerte['message']) ?></span></td>
                            <td class="d-none d-md-table-cell"><span class="truncate" title="<?= e(formatDate($alerte['date_alerte'])) ?>" data-bs-toggle="tooltip"><?= e(formatDate($alerte['date_alerte'])) ?></span></td>
                            <td class="text-end">
                                <form action="<?= url('alertes/' . $alerte['id_alerte'] . '/traiter') ?>" method="post">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-lg"></i> Traiter
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>
