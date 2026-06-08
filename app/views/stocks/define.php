<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-box-seam"></i> Définir les stocks</h1>
        <p class="text-muted">Saisir les stocks initiaux (FLOAT et CAISSE) pour chaque service.</p>
    </div>
    <a href="<?= url('stocks') ?>" class="btn btn-outline-secondary">Retour</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= url('stocks/define') ?>" method="post">
            <?= csrfField() ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Catégorie</th>
                            <th>Float</th>
                            <th>Caisse</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $s): ?>
                            <?php
                                $float = null; $caisse = null;
                                foreach ($s['soldes'] as $sd) {
                                    if ($sd['type_solde'] === 'FLOAT') $float = $sd;
                                    if ($sd['type_solde'] === 'CAISSE') $caisse = $sd;
                                }
                            ?>
                            <tr>
                                <td><?= e($s['nom']) ?></td>
                                <td><?= e($s['categorie']) ?></td>
                                <td>
                                    <?php if ($float): ?>
                                        <input type="number" step="0.01" name="montant[<?= e($float['id_solde']) ?>]" class="form-control" value="<?= e($float['montant_actuel']) ?>">
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($caisse): ?>
                                        <input type="number" step="0.01" name="montant[<?= e($caisse['id_solde']) ?>]" class="form-control" value="<?= e($caisse['montant_actuel']) ?>">
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label for="global_motif" class="form-label">Motif (raison des ajustements)</label>
                    <textarea name="global_motif" id="global_motif" class="form-control" rows="2" placeholder="Saisir une raison qui s'appliquera à tous les ajustements (obligatoire)"></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= url('stocks') ?>" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les stocks</button>
                </div>
            </div>
        </form>
    </div>
</div>
