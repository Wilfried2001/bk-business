<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-plus-circle"></i> Nouvelle transaction</h1>
        <p class="text-muted">Enregistrer une transaction pour un service.</p>
    </div>
    <a href="<?= url('transactions') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <i class="bi bi-journal-plus"></i> Nouvelle transaction
    </div>
    <div class="card-body">
        <form action="<?= url('transactions/store') ?>" method="post" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label for="id_service" class="form-label">Service</label>
                <select id="id_service" name="id_service" class="form-select" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= e($service['id_service']) ?>"><?= e($service['nom']) ?> (<?= e($service['categorie']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="id_type" class="form-label">Type d’opération</label>
                <select id="id_type" name="id_type" class="form-select" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($typesOperations as $type): ?>
                        <option value="<?= e($type['id_type']) ?>"><?= e($type['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="montant" class="form-label">Montant</label>
                <input type="number" step="0.01" min="0" id="montant" name="montant" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="reference" class="form-label">Référence</label>
                <input type="text" id="reference" name="reference" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="note" class="form-label">Note</label>
                <input type="text" id="note" name="note" class="form-control">
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
