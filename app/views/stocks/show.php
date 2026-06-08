<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-box-seam"></i> Stock — <?= e($service['nom']) ?></h1>
        <p class="text-muted"><?= e($service['categorie']) ?></p>
    </div>
    <a href="<?= url('stocks') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong><i class="bi bi-list-ul"></i> Historique des soldes</strong>
            <div class="text-muted small">Évolution du solde pour ce service.</div>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Type solde</th>
                    <th>Montant</th>
                    <th>Date de mise à jour</th>
                    <th>Seuil</th>
                    <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                        <th></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($soldes as $solde): ?>
                    <tr>
                        <td><?= e($solde['type_solde']) ?></td>
                        <td><?= e(formatMontant((float)$solde['montant_actuel'])) ?></td>
                        <td><?= e(formatDate($solde['date_maj'])) ?></td>
                        <td><?= $solde['valeur_seuil'] !== null ? e(formatMontant((float)$solde['valeur_seuil'])) : '—' ?></td>
                        <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                            <td>
                                <div class="d-flex gap-2 flex-column flex-sm-row align-items-center justify-content-end">
                                    <form action="<?= url('stocks/' . $service['id_service'] . '/seuil') ?>" method="post" class="d-flex gap-2 align-items-center">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_solde" value="<?= e($solde['id_solde']) ?>">
                                        <input type="hidden" name="redirect_to" value="stocks/<?= e($service['id_service']) ?>">
                                        <input type="number" name="valeur_seuil" class="form-control form-control-sm" step="0.01" value="<?= $solde['valeur_seuil'] !== null ? e($solde['valeur_seuil']) : '' ?>" placeholder="Seuil" required>
                                        <button type="submit" class="btn btn-sm btn-primary">Seuil</button>
                                    </form>

                                    <form action="<?= url('stocks/' . $service['id_service'] . '/solde') ?>" method="post" class="d-flex gap-2 align-items-center">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_solde" value="<?= e($solde['id_solde']) ?>">
                                        <input type="number" name="montant_actuel" class="form-control form-control-sm" step="0.01" value="<?= e($solde['montant_actuel']) ?>" placeholder="Montant" required>
                                        <input type="text" name="motif" class="form-control form-control-sm" placeholder="Motif (optionnel)" style="min-width:180px">
                                        <button type="submit" class="btn btn-sm btn-secondary">Mettre à jour</button>
                                    </form>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <i class="bi bi-clock-history"></i> Historique des seuils
    </div>
    <div class="card-body">
        <?php if (empty($seuilHistories)): ?>
            <p class="mb-0">Aucun historique de seuil enregistré pour ce service.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type solde</th>
                            <th>Ancien seuil</th>
                            <th>Nouveau seuil</th>
                            <th>Modifié par</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($seuilHistories as $history): ?>
                            <tr>
                                <td><?= e(formatDate($history['date_modification'], 'd/m/Y H:i')) ?></td>
                                <td><?= e($history['type_solde']) ?></td>
                                <td><?= e(formatMontant((float)$history['ancienne_valeur'])) ?></td>
                                <td><?= e(formatMontant((float)$history['nouvelle_valeur'])) ?></td>
                                <td><?= e($history['modifie_par_nom'] ?? 'Système') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
