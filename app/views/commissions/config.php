<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Paramétrage des commissions</h1>
        <p class="text-muted">Modifier les règles de calcul des commissions.</p>
    </div>
</div>
<div class="row g-4">
    <div class="col-12">
        <button class="btn btn-outline-primary mb-3" type="button" data-bs-toggle="collapse"
            data-bs-target="#newCommissionForm" aria-expanded="false" aria-controls="newCommissionForm">
            <i class="bi bi-plus-circle"></i> Paramétrer une commission
        </button>
        <div class="collapse" id="newCommissionForm">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Nouvelle configuration</h5>
                            <p class="text-muted small mb-0">Choisissez un service, un type d’opération, puis définissez
                                la règle de calcul.</p>
                        </div>
                    </div>
                    <form action="<?= url('commissions/config') ?>" method="post" class="row g-3 align-items-end">
                        <?= csrfField() ?>
                        <input type="hidden" name="id_config" value="0">
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-buildings"></i> Service</label>
                            <select name="id_service" class="form-select" required>
                                <option value="">Sélectionner un service</option>
                                <?php foreach ($services as $service): ?>
                                <option value="<?= e($service['id_service']) ?>"><?= e($service['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-list-ul"></i> Type opération</label>
                            <select name="id_type" class="form-select" required>
                                <option value="">Sélectionner un type</option>
                                <?php foreach ($types as $type): ?>
                                <option value="<?= e($type['id_type']) ?>"><?= e($type['libelle']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-card-text"></i> Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-list-task"></i> Source</label>
                            <select name="source" class="form-select">
                                <option value="OPERATEUR">OPERATEUR</option>
                                <option value="CLIENT">CLIENT</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-gear"></i> Mode calcul</label>
                            <select name="mode_calcul" class="form-select">
                                <option value="TAUX">TAUX</option>
                                <option value="FIXE">FIXE</option>
                                <option value="TRANCHE">TRANCHE</option>
                            </select>
                        </div>
                        <div class="col-md-3 commission-value-group">
                            <label class="form-label"><i class="bi bi-calculator"></i> Valeur</label>
                            <input type="number" name="valeur" class="form-control" step="0.0001" required>
                            <div class="form-text text-muted">Saisir un pourcentage si mode TAUX, ou un montant fixe si
                                mode FIXE.</div>
                        </div>
                        <div class="col-12 commission-tranche-section" style="display: none;">
                            <div class="card border-secondary-subtle mb-3">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong><i class="bi bi-diagram-3"></i> Barème TRANCHE</strong>
                                            <div class="text-muted small">Définissez les paliers et la commission fixe
                                                par intervalle.</div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary add-tranche-row">
                                            <i class="bi bi-plus-circle"></i> Ajouter une tranche
                                        </button>
                                    </div>
                                    <div class="tranche-rows"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if (empty($configs)): ?>
    <div class="col-12">
        <div class="alert alert-info mb-0">Aucune configuration de commission disponible.</div>
    </div>
    <?php else: ?>
    <?php foreach ($configs as $config): ?>
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= e($config['nom_service']) ?></strong>
                    <div class="text-muted small"><?= e($config['libelle_type']) ?></div>
                </div>
                <span class="badge bg-info text-white">Commission</span>
            </div>
            <div class="card-body">
                <form action="<?= url('commissions/config') ?>" method="post" class="row g-3 align-items-end">
                    <?= csrfField() ?>
                    <input type="hidden" name="id_config" value="<?= e($config['id_config']) ?>">
                    <div class="col-md-3">
                        <label class="form-label"><i class="bi bi-card-text"></i> Nom</label>
                        <input type="text" name="nom" class="form-control" value="<?= e($config['nom']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="bi bi-list-task"></i> Source</label>
                        <select name="source" class="form-select">
                            <option value="OPERATEUR" <?= $config['source'] === 'OPERATEUR' ? 'selected' : '' ?>>
                                OPERATEUR</option>
                            <option value="CLIENT" <?= $config['source'] === 'CLIENT' ? 'selected' : '' ?>>CLIENT
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="bi bi-gear"></i> Mode calcul</label>
                        <select name="mode_calcul" class="form-select">
                            <option value="TAUX" <?= $config['mode_calcul'] === 'TAUX' ? 'selected' : '' ?>>TAUX
                            </option>
                            <option value="FIXE" <?= $config['mode_calcul'] === 'FIXE' ? 'selected' : '' ?>>FIXE
                            </option>
                            <option value="TRANCHE" <?= $config['mode_calcul'] === 'TRANCHE' ? 'selected' : '' ?>>
                                TRANCHE</option>
                        </select>
                    </div>
                    <div class="col-md-3 commission-value-group">
                        <label class="form-label"><i class="bi bi-calculator"></i> Valeur</label>
                        <input type="number" name="valeur" class="form-control" step="0.0001"
                            value="<?= e($config['valeur']) ?>" required>
                        <div class="form-text text-muted">
                            Saisir un pourcentage si mode TAUX, ou un montant fixe si mode FIXE.
                        </div>
                    </div>
                    <div class="col-12 commission-tranche-section"
                        style="display: <?= e($config['mode_calcul'] === 'TRANCHE' ? 'block' : 'none') ?>;">
                        <div class="card border-secondary-subtle mb-3">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <strong><i class="bi bi-diagram-3"></i> Barème TRANCHE</strong>
                                        <div class="text-muted small">Définissez les paliers et la commission fixe par
                                            intervalle.</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary add-tranche-row">
                                        <i class="bi bi-plus-circle"></i> Ajouter une tranche
                                    </button>
                                </div>
                                <div class="tranche-rows">
                                    <?php if (!empty($config['tranches'])): ?>
                                    <?php foreach ($config['tranches'] as $tranche): ?>
                                    <div class="row g-3 mb-2 tranche-row">
                                        <div class="col-md-3">
                                            <label class="form-label">Montant min</label>
                                            <input type="number" name="tranches[montant_min][]" class="form-control"
                                                step="0.01" value="<?= e($tranche['montant_min']) ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Montant max</label>
                                            <input type="number" name="tranches[montant_max][]" class="form-control"
                                                step="0.01" value="<?= e($tranche['montant_max']) ?>">
                                            <div class="form-text">Laisser vide pour plafond infini.</div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Commission fixe</label>
                                            <input type="number" name="tranches[montant_fixe][]" class="form-control"
                                                step="0.01" value="<?= e($tranche['montant_fixe']) ?>" required>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="button"
                                                class="btn btn-outline-danger remove-tranche-row w-100">
                                                <i class="bi bi-trash"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <div class="tranche-rows"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>