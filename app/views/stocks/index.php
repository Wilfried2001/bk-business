<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-boxes"></i> Stocks</h1>
        <p class="text-muted">Suivi des soldes FLOAT et CAISSE par service.</p>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong><i class="bi bi-list-ul"></i> Soldes par service</strong>
            <div class="text-muted small">Vue consolidée des soldes par service.</div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($soldes)): ?>
            <p class="mb-0">Aucun solde disponible.</p>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-mobile-details">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th class="d-none d-md-table-cell">Catégorie</th>
                        <th class="d-none d-md-table-cell">Type</th>
                        <th>Montant</th>
                        <th>Seuil</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($soldes as $solde): ?>
                        <tr>
                            <td><span class="truncate" title="<?= e($solde['nom_service']) ?>" data-bs-toggle="tooltip"><?= e($solde['nom_service']) ?></span></td>
                            <td class="d-none d-md-table-cell"><span class="truncate" title="<?= e($solde['categorie']) ?>" data-bs-toggle="tooltip"><?= e($solde['categorie']) ?></span></td>
                            <td class="d-none d-md-table-cell"><span class="truncate" title="<?= e($solde['type_solde']) ?>" data-bs-toggle="tooltip"><?= e($solde['type_solde']) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$solde['montant_actuel'])) ?>" data-bs-toggle="tooltip"><?= e(formatMontant((float)$solde['montant_actuel'])) ?></span></td>
                            <td><span class="truncate" title="<?= $solde['valeur_seuil'] !== null ? e(formatMontant((float)$solde['valeur_seuil'])) : 'N/A' ?>" data-bs-toggle="tooltip"><?= $solde['valeur_seuil'] !== null ? e(formatMontant((float)$solde['valeur_seuil'])) : 'N/A' ?></span></td>
                            <td>
                                <span class="badge bg-<?= $solde['en_alerte'] ? 'danger' : 'success' ?>">
                                    <?= $solde['en_alerte'] ? 'Alerte' : 'Normal' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                                    <form action="<?= url('stocks/' . $solde['id_service'] . '/seuil') ?>" method="post" class="d-flex gap-2 justify-content-end align-items-center flex-wrap">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_solde" value="<?= e($solde['id_solde']) ?>">
                                        <input type="hidden" name="redirect_to" value="stocks">
                                        <input type="number" name="valeur_seuil" class="form-control form-control-sm" step="0.01" value="<?= $solde['valeur_seuil'] !== null ? e($solde['valeur_seuil']) : '' ?>" placeholder="Seuil" required style="width: 110px;">
                                        <button type="submit" class="btn btn-sm btn-primary">OK</button>
                                    </form>
                                <?php else: ?>
                                    <a href="<?= url('stocks/' . $solde['id_service']) ?>" class="btn btn-sm btn-outline-secondary">Détails</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>
