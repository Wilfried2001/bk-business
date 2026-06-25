<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="sliders-horizontal"></i> Seuils d'alerte par service</h1>
        <p class="text-gray-500">Configurez les seuils d'alerte pour tous les services en une seule page.</p>
    </div>
    <a href="<?= url('stocks') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
        <i data-lucide="arrow-left"></i> Retour
    </a>
</div>

<div class="app-card shadow-sm mb-4">
    <div class="app-card-header">
        <strong><i data-lucide="alert-triangle"></i> Gestion des seuils</strong>
        <div class="text-gray-500 small mt-1">Définissez les seuils d'alerte minimum pour les soldes FLOAT et CAISSE de chaque service.</div>
    </div>
    <div class="app-card-body">
        <?php if (empty($services)): ?>
            <p class="mb-0">Aucun service disponible.</p>
        <?php else: ?>
            <form action="<?= url('stocks/seuils/save') ?>" method="post">
                <?= csrfField() ?>

                <div class="overflow-x-auto">
                    <table class="app-table">
                        <thead class="app-table-light">
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
                                            <td class="hidden md:table-cell">
                                                <span class="inline-flex items-center px-2 py-1 app-badge app-badge-secondary"><?= e($service['categorie']) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($solde['type_solde'] === 'FLOAT'): ?>
                                                    <span class="inline-flex items-center px-2 py-1 app-badge app-badge-info">Float</span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2 py-1 app-badge app-badge-warning">Caisse</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= e(formatMontant((float)$solde['montant_actuel'])) ?>
                                            </td>
                                            <td>
                                                <?php if ($solde['valeur_seuil'] !== null): ?>
                                                    <span class="text-gray-500"><?= e(formatMontant((float)$solde['valeur_seuil'])) ?></span>
                                                <?php else: ?>
                                                    <span class="text-gray-500">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <input 
                                                    type="number" 
                                                    name="seuil[<?= e($solde['id_solde']) ?>]" 
                                                    class="border rounded-md px-3 py-2 text-sm w-full px-2 py-1 text-xs" 
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
                                        <td colspan="6" class="text-gray-500 text-center">
                                            <em>Aucun solde défini pour ce service</em>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
                        <i data-lucide="check-circle"></i> Enregistrer tous les seuils
                    </button>
                    <a href="<?= url('stocks') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
                        <i data-lucide="x-circle"></i> Annuler
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Aide et informations -->
<div class="app-alert app-alert-info app-alert-dismissible" role="alert">
    <h6 class="alert-heading"><i data-lucide="info"></i> Information</h6>
    <p class="mb-0">
        Le seuil d'alerte définit la valeur minimale en dessous de laquelle une alerte sera générée. 
        Si le solde actuel descend en dessous du seuil, l'opération sera marquée comme étant en alerte.
    </p>
    <button type="button" class="app-close" data-dismiss="alert" aria-label="Fermer"></button>
</div>
