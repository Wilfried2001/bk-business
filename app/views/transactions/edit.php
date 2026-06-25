<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="edit-2"></i> Modifier transaction #<?= e($transaction['id_transaction']) ?></h1>
        <p class="text-gray-500">Mettre à jour les informations de la transaction.</p>
    </div>
    <a href="<?= url('transactions/' . $transaction['id_transaction']) ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
        <i data-lucide="arrow-left"></i> Retour
    </a>
</div>
<div class="flex flex-wrap -mx-3 gy-4">
    <div class="lg:w-1/2 px-3">
        <div class="app-card shadow-sm mb-4">
            <div class="app-card-header">
                <i data-lucide="info"></i> Informations immuables
            </div>
            <div class="app-card-body">
                <dl class="flex flex-wrap -mx-3 mb-0">
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Service</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['nom_service']) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Type</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['libelle_type']) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Montant</dt>
                    <dd class="sm:w-7/12 px-3"><?= e(formatMontant((float)$transaction['montant'])) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Agent</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['nom_agent']) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Date</dt>
                    <dd class="sm:w-7/12 px-3"><?= e(formatDate($transaction['date_heure'])) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Statut</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['statut']) ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="lg:w-1/2 px-3">
        <div class="app-card shadow-sm mb-4">
            <div class="app-card-header">
                <i data-lucide="edit-2"></i> Modifier les détails
            </div>
            <div class="app-card-body">
                <form action="<?= url('transactions/' . $transaction['id_transaction'] . '/update') ?>" method="post" class="flex flex-wrap -mx-3 gap-4 transaction-edit-form" novalidate>
                    <?= csrfField() ?>
                    <div class="w-full px-3">
                        <label for="reference" class="app-label">Référence</label>
                        <input type="text" id="reference" name="reference" class="border rounded-md px-3 py-2 text-sm w-full" maxlength="255" value="<?= e($transaction['reference']) ?>">
                        <div class="invalid-feedback">La référence doit faire moins de 255 caractères.</div>
                    </div>
                    <div class="w-full px-3">
                        <label for="note" class="app-label">Note</label>
                        <textarea id="note" name="note" class="border rounded-md px-3 py-2 text-sm w-full" rows="4" maxlength="1000"><?= e($transaction['note']) ?></textarea>
                        <div class="invalid-feedback">La note doit faire moins de 1000 caractères.</div>
                    </div>
                    <div class="w-full px-3 text-right">
                        <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
                            <i data-lucide="save"></i> Enregistrer
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
