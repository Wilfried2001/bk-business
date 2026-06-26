<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Paramétrage / Commissions</p>
            <h1>Paramétrage des commissions</h1>
            <p>Configurer les taux, montants fixes et tranches par service.</p>
        </div>
        <div class="bk-actions">
            <button class="bk-btn bk-btn-primary" type="button" data-toggle="collapse" data-target="#newCommissionForm" aria-expanded="false" aria-controls="newCommissionForm">
                <i data-lucide="plus"></i> Nouvelle configuration
            </button>
        </div>
    </section>

    <section class="app-collapse" id="newCommissionForm">
        <div class="app-card">
            <div class="app-card-header">
                <span><i data-lucide="plus-circle"></i> Nouvelle configuration</span>
            </div>
            <div class="app-card-body">
                <form action="<?= url('commissions/config') ?>" method="post" class="bk-form-grid">
                    <?= csrfField() ?>
                    <input type="hidden" name="id_config" value="0">

                    <div>
                        <label class="bk-label">Service</label>
                        <select name="id_service" class="bk-select" required>
                            <option value="">Sélectionner un service</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= e($service['id_service']) ?>"><?= e($service['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="bk-label">Type opération</label>
                        <select name="id_type" class="bk-select" required>
                            <option value="">Sélectionner un type</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= e($type['id_type']) ?>"><?= e($type['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="bk-label">Nom</label>
                        <input type="text" name="nom" class="bk-field" required>
                    </div>

                    <div>
                        <label class="bk-label">Source</label>
                        <select name="source" class="bk-select">
                            <option value="OPERATEUR">OPERATEUR</option>
                            <option value="CLIENT">CLIENT</option>
                        </select>
                    </div>

                    <div>
                        <label class="bk-label">Mode calcul</label>
                        <select name="mode_calcul" class="bk-select">
                            <option value="TAUX">TAUX</option>
                            <option value="FIXE">FIXE</option>
                            <option value="TRANCHE">TRANCHE</option>
                        </select>
                    </div>

                    <div class="commission-value-group">
                        <label class="bk-label">Valeur</label>
                        <input type="number" name="valeur" class="bk-field app-field" step="0.0001" required>
                        <div class="app-help">Pourcentage si TAUX, montant si FIXE.</div>
                    </div>

                    <div class="commission-tranche-section" style="display: none; grid-column: 1 / -1;">
                        <div class="app-card">
                            <div class="app-card-header">
                                <span><i data-lucide="workflow"></i> Barème tranche</span>
                                <button type="button" class="bk-btn bk-btn-secondary add-tranche-row">
                                    <i data-lucide="plus"></i> Ajouter
                                </button>
                            </div>
                            <div class="app-card-body tranche-rows"></div>
                        </div>
                    </div>

                    <div class="bk-actions justify-end" style="grid-column: 1 / -1;">
                        <button type="submit" class="bk-btn bk-btn-primary">
                            <i data-lucide="save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php if (empty($configs)): ?>
        <section class="app-card">
            <div class="bk-empty">
                <i data-lucide="settings"></i>
                <strong>Aucune configuration disponible</strong>
                <span>Créez une première règle de commission.</span>
            </div>
        </section>
    <?php else: ?>
        <?php foreach ($configs as $config): ?>
            <section class="app-card">
                <div class="app-card-header">
                    <span>
                        <i data-lucide="badge-percent"></i>
                        <?= e($config['nom_service']) ?> · <?= e($config['libelle_type']) ?>
                    </span>
                    <span class="bk-status info"><?= e($config['mode_calcul']) ?></span>
                </div>
                <div class="app-card-body">
                    <form action="<?= url('commissions/config') ?>" method="post" class="bk-form-grid">
                        <?= csrfField() ?>
                        <input type="hidden" name="id_config" value="<?= e($config['id_config']) ?>">

                        <div>
                            <label class="bk-label">Nom</label>
                            <input type="text" name="nom" class="bk-field" value="<?= e($config['nom']) ?>" required>
                        </div>

                        <div>
                            <label class="bk-label">Source</label>
                            <select name="source" class="bk-select">
                                <option value="OPERATEUR" <?= $config['source'] === 'OPERATEUR' ? 'selected' : '' ?>>OPERATEUR</option>
                                <option value="CLIENT" <?= $config['source'] === 'CLIENT' ? 'selected' : '' ?>>CLIENT</option>
                            </select>
                        </div>

                        <div>
                            <label class="bk-label">Mode calcul</label>
                            <select name="mode_calcul" class="bk-select">
                                <option value="TAUX" <?= $config['mode_calcul'] === 'TAUX' ? 'selected' : '' ?>>TAUX</option>
                                <option value="FIXE" <?= $config['mode_calcul'] === 'FIXE' ? 'selected' : '' ?>>FIXE</option>
                                <option value="TRANCHE" <?= $config['mode_calcul'] === 'TRANCHE' ? 'selected' : '' ?>>TRANCHE</option>
                            </select>
                        </div>

                        <div class="commission-value-group">
                            <label class="bk-label">Valeur</label>
                            <input type="number" name="valeur" class="bk-field app-field" step="0.0001" value="<?= e($config['valeur']) ?>" required>
                            <div class="app-help">Pourcentage si TAUX, montant si FIXE.</div>
                        </div>

                        <div class="commission-tranche-section" style="display: <?= e($config['mode_calcul'] === 'TRANCHE' ? 'block' : 'none') ?>; grid-column: 1 / -1;">
                            <div class="app-card">
                                <div class="app-card-header">
                                    <span><i data-lucide="workflow"></i> Tranches de commission</span>
                                    <button type="button" class="bk-btn bk-btn-secondary add-tranche-row">
                                        <i data-lucide="plus"></i> Ajouter une tranche
                                    </button>
                                </div>
                                <div class="app-card-body tranche-rows">
                                    <?php if (!empty($config['tranches'])): ?>
                                        <?php foreach ($config['tranches'] as $tranche): ?>
                                            <div class="grid gap-3 md:grid-cols-4 tranche-row mb-2">
                                                <div>
                                                    <label class="bk-label">Montant min</label>
                                                    <input type="number" name="tranches[montant_min][]" class="bk-field app-field" step="0.01" value="<?= e($tranche['montant_min']) ?>" required>
                                                </div>
                                                <div>
                                                    <label class="bk-label">Montant max</label>
                                                    <input type="number" name="tranches[montant_max][]" class="bk-field app-field" step="0.01" value="<?= e($tranche['montant_max'] ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label class="bk-label">Commission fixe</label>
                                                    <input type="number" name="tranches[montant_fixe][]" class="bk-field app-field" step="0.01" value="<?= e($tranche['montant_fixe']) ?>" required>
                                                </div>
                                                <div class="flex items-end">
                                                    <button type="button" class="bk-btn bk-btn-danger remove-tranche-row w-full">
                                                        <i data-lucide="trash"></i> Supprimer
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="bk-actions justify-end" style="grid-column: 1 / -1;">
                            <button type="submit" class="bk-btn bk-btn-primary">
                                <i data-lucide="save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
