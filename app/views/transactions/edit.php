<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-pencil-square"></i> Modifier transaction #<?= e($transaction['id_transaction']) ?></h1>
        <p class="text-muted">Mettre à jour les informations de la transaction.</p>
    </div>
    <a href="<?= url('transactions/' . $transaction['id_transaction']) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>
<div class="row gy-4">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations immuables
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Service</dt>
                    <dd class="col-sm-7"><?= e($transaction['nom_service']) ?></dd>
                    <dt class="col-sm-5">Type</dt>
                    <dd class="col-sm-7"><?= e($transaction['libelle_type']) ?></dd>
                    <dt class="col-sm-5">Montant</dt>
                    <dd class="col-sm-7"><?= e(formatMontant((float)$transaction['montant'])) ?></dd>
                    <dt class="col-sm-5">Agent</dt>
                    <dd class="col-sm-7"><?= e($transaction['nom_agent']) ?></dd>
                    <dt class="col-sm-5">Date</dt>
                    <dd class="col-sm-7"><?= e(formatDate($transaction['date_heure'])) ?></dd>
                    <dt class="col-sm-5">Statut</dt>
                    <dd class="col-sm-7"><?= e($transaction['statut']) ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-pencil-square"></i> Modifier les détails
            </div>
            <div class="card-body">
                <form action="<?= url('transactions/' . $transaction['id_transaction'] . '/update') ?>" method="post" class="row g-3 transaction-edit-form" novalidate>
                    <?= csrfField() ?>
                    <div class="col-md-12">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" id="reference" name="reference" class="form-control" maxlength="255" value="<?= e($transaction['reference']) ?>">
                        <div class="invalid-feedback">La référence doit faire moins de 255 caractères.</div>
                    </div>
                    <div class="col-md-12">
                        <label for="note" class="form-label">Note</label>
                        <textarea id="note" name="note" class="form-control" rows="4" maxlength="1000"><?= e($transaction['note']) ?></textarea>
                        <div class="invalid-feedback">La note doit faire moins de 1000 caractères.</div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const form = document.querySelector('.transaction-edit-form');
                    if (!form) return;

                    form.addEventListener('submit', function (event) {
                        const reference = form.querySelector('#reference');
                        const note = form.querySelector('#note');

                        if (reference) {
                            reference.value = reference.value.trim();
                        }
                        if (note) {
                            note.value = note.value.trim();
                        }

                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    });
                });
            </script>
        </div>
    </div>
</div>
