<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Transactions / Modifier</p>
            <h1>Modifier transaction #<?= e($transaction['id_transaction']) ?></h1>
            <p>Mettre à jour la référence et la note sans modifier les informations métier.</p>
        </div>
        <div class="bk-actions">
            <a href="<?= url('transactions/' . $transaction['id_transaction']) ?>" class="bk-btn bk-btn-secondary">
                <i data-lucide="arrow-left"></i> Retour
            </a>
        </div>
    </section>

    <section class="bk-kpis">
        <article class="bk-kpi">
            <div class="bk-kpi-icon"><i data-lucide="radio-tower"></i></div>
            <p>Service</p>
            <strong><?= e($transaction['nom_service']) ?></strong>
            <span><?= e($transaction['libelle_type']) ?></span>
        </article>
        <article class="bk-kpi green">
            <div class="bk-kpi-icon"><i data-lucide="banknote"></i></div>
            <p>Montant</p>
            <strong><?= e(formatMontant((float)$transaction['montant'])) ?></strong>
            <span><?= e($transaction['statut']) ?></span>
        </article>
        <article class="bk-kpi sky">
            <div class="bk-kpi-icon"><i data-lucide="user"></i></div>
            <p>Agent</p>
            <strong><?= e($transaction['nom_agent']) ?></strong>
            <span><?= e(formatDate($transaction['date_heure'])) ?></span>
        </article>
        <article class="bk-kpi amber">
            <div class="bk-kpi-icon"><i data-lucide="lock"></i></div>
            <p>Champs verrouillés</p>
            <strong>4</strong>
            <span>Service, type, montant, agent</span>
        </article>
    </section>

    <section class="app-card">
        <div class="app-card-header"><span><i data-lucide="edit-2"></i> Détails modifiables</span></div>
        <div class="app-card-body">
            <form action="<?= url('transactions/' . $transaction['id_transaction'] . '/update') ?>" method="post" class="bk-page transaction-edit-form" novalidate>
                <?= csrfField() ?>
                <div>
                    <label for="reference" class="bk-label">Référence</label>
                    <input type="text" id="reference" name="reference" class="bk-field" maxlength="255" value="<?= e($transaction['reference']) ?>">
                    <div class="invalid-feedback">La référence doit faire moins de 255 caractères.</div>
                </div>
                <div>
                    <label for="note" class="bk-label">Note</label>
                    <textarea id="note" name="note" class="bk-field" rows="5" maxlength="1000"><?= e($transaction['note']) ?></textarea>
                    <div class="invalid-feedback">La note doit faire moins de 1000 caractères.</div>
                </div>
                <div class="bk-actions justify-end">
                    <a href="<?= url('transactions/' . $transaction['id_transaction']) ?>" class="bk-btn bk-btn-secondary">
                        <i data-lucide="x"></i> Annuler
                    </a>
                    <button type="submit" class="bk-btn bk-btn-primary">
                        <i data-lucide="save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<script nonce="<?= e(cspNonce()) ?>">
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('.transaction-edit-form');
        if (!form) return;

        form.addEventListener('submit', function (event) {
            const reference = form.querySelector('#reference');
            const note = form.querySelector('#note');

            if (reference) reference.value = reference.value.trim();
            if (note) note.value = note.value.trim();

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
</script>
