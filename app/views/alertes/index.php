<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="alert-triangle"></i> Alertes</h1>
        <p class="text-gray-500">Aperçu des alertes de solde actives.</p>
    </div>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header flex justify-between items-center">
        <div>
            <strong><i data-lucide="alert-circle"></i> Alerte(s) actives</strong>
            <div class="text-gray-500 small">Liste des alertes en cours et actions disponibles.</div>
        </div>
    </div>
    <div class="app-card-body">
        <?php if (empty($alertes)): ?>
            <p class="mb-0">Aucune alerte active.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
            <table class="app-table table-mobile-details">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Solde</th>
                        <th>Montant actuel</th>
                        <th>Seuil</th>
                        <th>Message</th>
                        <th class="hidden md:table-cell">Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alertes as $alerte): ?>
                        <tr>
                            <td><span class="truncate" title="<?= e($alerte['nom_service']) ?>" data-toggle="tooltip"><?= e($alerte['nom_service']) ?></span></td>
                            <td><span class="truncate" title="<?= e($alerte['type_solde']) ?>" data-toggle="tooltip"><?= e($alerte['type_solde']) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$alerte['montant_actuel'])) ?>" data-toggle="tooltip"><?= e(formatMontant((float)$alerte['montant_actuel'])) ?></span></td>
                            <td><span class="truncate" title="<?= e(formatMontant((float)$alerte['valeur_seuil'])) ?>" data-toggle="tooltip"><?= e(formatMontant((float)$alerte['valeur_seuil'])) ?></span></td>
                            <td><span class="truncate" title="<?= e($alerte['message']) ?>" data-toggle="tooltip"><?= e($alerte['message']) ?></span></td>
                            <td class="hidden md:table-cell"><span class="truncate" title="<?= e(formatDate($alerte['date_alerte'])) ?>" data-toggle="tooltip"><?= e(formatDate($alerte['date_alerte'])) ?></span></td>
                            <td class="text-right">
                                <form action="<?= url('alertes/' . $alerte['id_alerte'] . '/traiter') ?>" method="post">
                                    <?= csrfField() ?>
                                    <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs bg-green-600 text-white">
                                        <i data-lucide="check"></i> Traiter
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
