<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-sliders"></i> Seuils d'alerte par service</h1>
        <p class="text-muted">Configurez les seuils d'alerte pour tous les services en une seule page.</p>
    </div>
    <a href="<?= url('stocks') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <strong><i class="bi bi-exclamation-triangle"></i> Gestion des seuils</strong>
        <div class="text-muted small mt-1">Définissez les seuils d'alerte minimum pour les soldes FLOAT et CAISSE de chaque service.</div>
    </div>
    <div class="card-body">
        <?php if (empty($services)): ?>
            <p class="mb-0">Aucun service disponible.</p>
        <?php else: ?>
            <form action="<?= url('stocks/seuils/save') ?>" method="post">
                <?= csrfField() ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Service</th>
                                <th>Catégorie</th>
                                <th>Type de solde</th>
                                <th>Solde actuel</th>
                                <th>Seuil actuel</th>
                                <th>Nouveau seuil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                                <?php if (!empty($service['soldes'])): ?>
                                    <?php foreach ($service['soldes'] as $solde): ?>
                                        <tr>
                                            <td>
                                                <strong><?= e($service['nom']) ?></strong>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="badge bg-light text-dark"><?= e($service['categorie']) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($solde['type_solde'] === 'FLOAT'): ?>
                                                    <span class="badge bg-info">Float</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Caisse</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= e(formatMontant((float)$solde['montant_actuel'])) ?>
                                            </td>
                                            <td>
                                                <?php if ($solde['valeur_seuil'] !== null): ?>
                                                    <span class="text-muted"><?= e(formatMontant((float)$solde['valeur_seuil'])) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <input 
                                                    type="number" 
                                                    name="seuil[<?= e($solde['id_solde']) ?>]" 
                                                    class="form-control form-control-sm" 
                                                    step="0.01" 
                                                    min="0"
                                                    value="<?= $solde['valeur_seuil'] !== null ? e($solde['valeur_seuil']) : '' ?>" 
                                                    placeholder="Entrez un seuil"
                                                >
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-muted text-center">
                                            <em>Aucun solde défini pour ce service</em>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Enregistrer tous les seuils
                    </button>
                    <a href="<?= url('stocks') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Annuler
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Aide et informations -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Information</h6>
    <p class="mb-0">
        Le seuil d'alerte définit la valeur minimale en dessous de laquelle une alerte sera générée. 
        Si le solde actuel descend en dessous du seuil, l'opération sera marquée comme étant en alerte.
    </p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
</div>
