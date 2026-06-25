<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="package"></i> Définir les stocks</h1>
        <p class="text-gray-500">Saisir les stocks initiaux (FLOAT et CAISSE) pour chaque service.</p>
    </div>
    <a href="<?= url('stocks') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
        <i data-lucide="arrow-left"></i> Retour
    </a>
</div>

<div class="app-card shadow-sm">
    <div class="app-card-body">
        <form action="<?= url('stocks/define') ?>" method="post">
            <?= csrfField() ?>
            <div class="overflow-x-auto">
                <table class="app-table app-table-striped">
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
                                        <input type="number" step="0.01" name="montant[<?= e($float['id_solde']) ?>]" class="border rounded-md px-3 py-2 text-sm w-full" value="<?= e($float['montant_actuel']) ?>">
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($caisse): ?>
                                        <input type="number" step="0.01" name="montant[<?= e($caisse['id_solde']) ?>]" class="border rounded-md px-3 py-2 text-sm w-full" value="<?= e($caisse['montant_actuel']) ?>">
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
                    <label for="global_motif" class="app-label">Motif (raison des ajustements)</label>
                    <textarea name="global_motif" id="global_motif" class="border rounded-md px-3 py-2 text-sm w-full" rows="2" placeholder="Saisir une raison qui s'appliquera à tous les ajustements (obligatoire)"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="<?= url('stocks') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
                        <i data-lucide="x-circle"></i> Annuler
                    </a>
                    <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
                        <i data-lucide="save"></i> Enregistrer les stocks
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
