<div class="bk-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Transferts / Float</p>
            <h1>Nouvelle demande de transfert de float</h1>
            <p>Échangez du float entre agences pour un même service.</p>
        </div>
        <div class="bk-actions">
            <a href="<?= url('transferts-float') ?>" class="bk-btn bk-btn-secondary">
                <i data-lucide="arrow-left"></i> Retour
            </a>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-body">
            <form action="<?= url('transferts-float/store') ?>" method="post">
                <?= csrfField() ?>

                <div class="bk-form-grid">
                    <div>
                        <label for="id_agence_source" class="bk-label">Agence source</label>
                        <select id="id_agence_source" name="id_agence_source" class="bk-field" required>
                            <option value="">Sélectionnez l'agence source</option>
                            <?php foreach ($agences as $agence): ?>
                                <option value="<?= e($agence['id_agence']) ?>"><?= e($agence['nom'] . ' - ' . ($agence['ville'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="id_agence_destination" class="bk-label">Agence destination</label>
                        <select id="id_agence_destination" name="id_agence_destination" class="bk-field" required>
                            <option value="">Sélectionnez l'agence destination</option>
                            <?php foreach ($agences as $agence): ?>
                                <option value="<?= e($agence['id_agence']) ?>"><?= e($agence['nom'] . ' - ' . ($agence['ville'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="id_service" class="bk-label">Service</label>
                        <select id="id_service" name="id_service" class="bk-field" required>
                            <option value="">Sélectionnez le service</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= e($service['id_service']) ?>"><?= e($service['nom'] . ' (' . $service['categorie'] . ')') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="montant" class="bk-label">Montant</label>
                        <input type="number" step="0.01" min="1" id="montant" name="montant" class="bk-field" placeholder="0.00" required>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="motif" class="bk-label">Motif</label>
                    <textarea id="motif" name="motif" class="bk-field" rows="4" maxlength="255" placeholder="Ex: Renfort de float pour la journée"></textarea>
                </div>

                <div class="bk-actions justify-between mt-6">
                    <a href="<?= url('transferts-float') ?>" class="bk-btn bk-btn-secondary">
                        <i data-lucide="x-circle"></i> Annuler
                    </a>
                    <button type="submit" class="bk-btn bk-btn-primary">
                        <i data-lucide="send"></i> Envoyer la demande
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
