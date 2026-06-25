<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="plus-circle"></i> Nouvelle transaction</h1>
        <p class="text-gray-500">Enregistrer une transaction pour un service.</p>
    </div>
    <a href="<?= url('transactions') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
        <i data-lucide="arrow-left"></i> Retour
    </a>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header">
        <i data-lucide="file-plus"></i> Nouvelle transaction
    </div>
    <div class="app-card-body">
        <form action="<?= url('transactions/store') ?>" method="post" class="flex flex-wrap -mx-3 gap-4">
            <?= csrfField() ?>
            <div class="md:w-1/2 px-3">
                <label for="id_service" class="app-label">Service</label>
                <select id="id_service" name="id_service" class="border rounded-md px-3 py-2 text-sm w-full bg-white" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= e($service['id_service']) ?>"><?= e($service['nom']) ?> (<?= e($service['categorie']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:w-1/2 px-3">
                <label for="id_type" class="app-label">Type d’opération</label>
                <select id="id_type" name="id_type" class="border rounded-md px-3 py-2 text-sm w-full bg-white" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($typesOperations as $type): ?>
                        <option value="<?= e($type['id_type']) ?>"><?= e($type['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:w-1/3 px-3">
                <label for="montant" class="app-label">Montant</label>
                <input type="number" step="0.01" min="0" id="montant" name="montant" class="border rounded-md px-3 py-2 text-sm w-full" required>
            </div>
            <div class="md:w-1/3 px-3">
                <label for="reference" class="app-label">Référence</label>
                <input type="text" id="reference" name="reference" class="border rounded-md px-3 py-2 text-sm w-full">
            </div>
            <div class="md:w-1/3 px-3">
                <label for="note" class="app-label">Note</label>
                <input type="text" id="note" name="note" class="border rounded-md px-3 py-2 text-sm w-full">
            </div>
            <div class="w-full px-3 text-right">
                <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
                    <i data-lucide="check-circle"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
