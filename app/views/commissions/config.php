<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Paramétrage des commissions</h1>
        <p class="text-gray-500">Modifier les règles de calcul des commissions.</p>
    </div>
</div>
<div class="flex flex-wrap -mx-3 g-4">
    <div class="w-full px-3">
        <button class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-[#0d47a1] text-[#0d47a1] bg-transparent hover:bg-[#eef4ff] mb-3" type="button" data-toggle="collapse"
            data-target="#newCommissionForm" aria-expanded="false" aria-controls="newCommissionForm">
            <i data-lucide="plus-circle"></i> Paramétrer une commission
        </button>
        <div class="app-collapse" id="newCommissionForm">
            <div class="app-card shadow-sm mb-4">
                <div class="app-card-body">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <h5 class="mb-1">Nouvelle configuration</h5>
                            <p class="text-gray-500 small mb-0">Choisissez un service, un type d’opération, puis définissez
                                la règle de calcul.</p>
                        </div>
                    </div>
                    <form action="<?= url('commissions/config') ?>" method="post" class="flex flex-wrap -mx-3 gap-4 items-end">
                        <?= csrfField() ?>
                        <input type="hidden" name="id_config" value="0">
                        <div class="md:w-1/4 px-3">
                            <label class="app-label"><i data-lucide="building-2"></i> Service</label>
                            <select name="id_service" class="border rounded-md px-3 py-2 text-sm w-full bg-white" required>
                                <option value="">Sélectionner un service</option>
                                <?php foreach ($services as $service): ?>
                                <option value="<?= e($service['id_service']) ?>"><?= e($service['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:w-1/4 px-3">
                            <label class="app-label"><i data-lucide="list"></i> Type opération</label>
                            <select name="id_type" class="border rounded-md px-3 py-2 text-sm w-full bg-white" required>
                                <option value="">Sélectionner un type</option>
                                <?php foreach ($types as $type): ?>
                                <option value="<?= e($type['id_type']) ?>"><?= e($type['libelle']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:w-1/4 px-3">
                            <label class="app-label"><i data-lucide="file-text"></i> Nom</label>
                            <input type="text" name="nom" class="border rounded-md px-3 py-2 text-sm w-full" required>
                        </div>
                        <div class="md:w-1/4 px-3">
                            <label class="app-label"><i data-lucide="list-check"></i> Source</label>
                            <select name="source" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                                <option value="OPERATEUR">OPERATEUR</option>
                                <option value="CLIENT">CLIENT</option>
                            </select>
                        </div>
                        <div class="md:w-1/4 px-3">
                            <label class="app-label"><i data-lucide="settings"></i> Mode calcul</label>
                            <select name="mode_calcul" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                                <option value="TAUX">TAUX</option>
                                <option value="FIXE">FIXE</option>
                                <option value="TRANCHE">TRANCHE</option>
                            </select>
                        </div>
                        <div class="md:w-1/4 px-3 commission-value-group">
                            <label class="app-label"><i data-lucide="calculator"></i> Valeur</label>
                            <input type="number" name="valeur" class="border rounded-md px-3 py-2 text-sm w-full" step="0.0001" required>
                            <div class="app-help text-gray-500">Saisir un pourcentage si mode TAUX, ou un montant fixe si
                                mode FIXE.</div>
                        </div>
                        <div class="w-full px-3 commission-tranche-section" style="display: none;">
                            <div class="app-card border-secondary-subtle mb-3">
                                <div class="app-card-body py-3">
                                    <div class="flex justify-between items-center mb-3">
                                        <div>
                                            <strong><i data-lucide="workflow"></i> Barème TRANCHE</strong>
                                            <div class="text-gray-500 small">Définissez les paliers et la commission fixe
                                                par intervalle.</div>
                                        </div>
                                        <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-[#0d47a1] text-[#0d47a1] bg-transparent hover:bg-[#eef4ff] add-tranche-row">
                                            <i data-lucide="plus-circle"></i> Ajouter une tranche
                                        </button>
                                    </div>
                                    <div class="tranche-rows"></div>
                                </div>
                            </div>
                        </div>
                        <div class="md:w-1/4 px-3 text-right">
                            <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white w-full">
                                <i data-lucide="save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if (empty($configs)): ?>
    <div class="w-full px-3">
        <div class="app-alert app-alert-info mb-0">Aucune configuration de commission disponible.</div>
    </div>
    <?php else: ?>
    <?php foreach ($configs as $config): ?>
    <div class="w-full px-3">
        <div class="app-card mb-3">
            <div class="app-card-header flex justify-between items-center">
                <div>
                    <strong><?= e($config['nom_service']) ?></strong>
                    <div class="text-gray-500 small"><?= e($config['libelle_type']) ?></div>
                </div>
                <span class="inline-flex items-center px-2 py-1 app-badge app-badge-info">Commission</span>
            </div>
            <div class="app-card-body">
                <form action="<?= url('commissions/config') ?>" method="post" class="flex flex-wrap -mx-3 gap-4 items-end">
                    <?= csrfField() ?>
                    <input type="hidden" name="id_config" value="<?= e($config['id_config']) ?>">
                    <div class="md:w-1/4 px-3">
                        <label class="app-label"><i data-lucide="file-text"></i> Nom</label>
                        <input type="text" name="nom" class="border rounded-md px-3 py-2 text-sm w-full" value="<?= e($config['nom']) ?>" required>
                    </div>
                    <div class="md:w-1/4 px-3">
                        <label class="app-label"><i data-lucide="list-check"></i> Source</label>
                        <select name="source" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                            <option value="OPERATEUR" <?= $config['source'] === 'OPERATEUR' ? 'selected' : '' ?>>
                                OPERATEUR</option>
                            <option value="CLIENT" <?= $config['source'] === 'CLIENT' ? 'selected' : '' ?>>CLIENT
                            </option>
                        </select>
                    </div>
                    <div class="md:w-1/4 px-3">
                        <label class="app-label"><i data-lucide="settings"></i> Mode calcul</label>
                        <select name="mode_calcul" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                            <option value="TAUX" <?= $config['mode_calcul'] === 'TAUX' ? 'selected' : '' ?>>TAUX
                            </option>
                            <option value="FIXE" <?= $config['mode_calcul'] === 'FIXE' ? 'selected' : '' ?>>FIXE
                            </option>
                            <option value="TRANCHE" <?= $config['mode_calcul'] === 'TRANCHE' ? 'selected' : '' ?>>
                                TRANCHE</option>
                        </select>
                    </div>
                    <div class="md:w-1/4 px-3 commission-value-group">
                        <label class="app-label"><i data-lucide="calculator"></i> Valeur</label>
                        <input type="number" name="valeur" class="border rounded-md px-3 py-2 text-sm w-full" step="0.0001"
                            value="<?= e($config['valeur']) ?>" required>
                        <div class="app-help text-gray-500">
                            Saisir un pourcentage si mode TAUX, ou un montant fixe si mode FIXE.
                        </div>
                    </div>
                    <div class="w-full px-3 commission-tranche-section"
                        style="display: <?= e($config['mode_calcul'] === 'TRANCHE' ? 'block' : 'none') ?>;">
                        <div class="app-card border-secondary-subtle mb-3">
                            <div class="app-card-body py-3">
                                <div class="flex justify-between items-center mb-3">
                                    <div>
                                        <strong><i data-lucide="workflow"></i> Barème TRANCHE</strong>
                                        <div class="text-gray-500 small">Définissez les paliers et la commission fixe par
                                            intervalle.</div>
                                    </div>
                                    <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-[#0d47a1] text-[#0d47a1] bg-transparent hover:bg-[#eef4ff] add-tranche-row">
                                        <i data-lucide="plus-circle"></i> Ajouter une tranche
                                    </button>
                                </div>
                                <div class="tranche-rows">
                                    <?php if (!empty($config['tranches'])): ?>
                                    <?php foreach ($config['tranches'] as $tranche): ?>
                                    <div class="flex flex-wrap -mx-3 gap-4 mb-2 tranche-row">
                                        <div class="md:w-1/4 px-3">
                                            <label class="app-label">Montant min</label>
                                            <input type="number" name="tranches[montant_min][]" class="border rounded-md px-3 py-2 text-sm w-full"
                                                step="0.01" value="<?= e($tranche['montant_min']) ?>" required>
                                        </div>
                                        <div class="md:w-1/4 px-3">
                                            <label class="app-label">Montant max</label>
                                            <input type="number" name="tranches[montant_max][]" class="border rounded-md px-3 py-2 text-sm w-full"
                                                step="0.01" value="<?= e($tranche['montant_max'] ?? '') ?>">
                                            <div class="app-help">Laisser vide pour plafond infini.</div>
                                        </div>
                                        <div class="md:w-1/4 px-3">
                                            <label class="app-label">Commission fixe</label>
                                            <input type="number" name="tranches[montant_fixe][]" class="border rounded-md px-3 py-2 text-sm w-full"
                                                step="0.01" value="<?= e($tranche['montant_fixe']) ?>" required>
                                        </div>
                                        <div class="md:w-1/4 px-3 flex items-end">
                                            <button type="button"
                                                class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-[#d32f2f] text-[#d32f2f] bg-transparent hover:bg-[#fff1f0] remove-tranche-row w-full">
                                                <i data-lucide="trash"></i> Supprimer
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
                    <div class="md:w-1/4 px-3 text-right">
                        <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white w-full">
                            <i data-lucide="save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
