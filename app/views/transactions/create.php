<?php
    $serviceLogo = function (string $name): array {
        $normalized = strtolower($name);
        if (str_contains($normalized, 'orange')) return ['label' => 'OM', 'class' => 'orange'];
        if (str_contains($normalized, 'mtn')) return ['label' => 'MTN', 'class' => 'mtn'];
        if (str_contains($normalized, 'western')) return ['label' => 'WU', 'class' => 'wu'];
        if (str_contains($normalized, 'dhl')) return ['label' => 'DHL', 'class' => 'dhl'];
        if (str_contains($normalized, 'canal')) return ['label' => 'C+', 'class' => 'canal'];
        if (str_contains($normalized, 'ria')) return ['label' => 'Ria', 'class' => 'ria'];
        if (str_contains($normalized, 'eneo')) return ['label' => 'EN', 'class' => 'eneo'];

        $parts = preg_split('/\s+/', trim($name));
        $letters = '';
        foreach (array_slice($parts ?: [], 0, 2) as $part) {
            $letters .= substr($part, 0, 1);
        }
        return ['label' => strtoupper($letters ?: 'BK'), 'class' => 'default'];
    };
?>

<div class="bk-page tx-create-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Transactions / Nouvelle</p>
            <h1>Nouvelle transaction</h1>
            <p>Choisissez le service, puis l'opération, le montant, les références et validez le résumé.</p>
        </div>
        <div class="bk-actions">
            <a href="<?= url('transactions') ?>" class="bk-btn bk-btn-secondary">
                <i data-lucide="arrow-left"></i> Retour
            </a>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-body">
            <div class="tx-stepper" aria-label="Progression de la transaction">
                <button type="button" class="tx-step active" data-step-button="1"><b>1</b><span>Service</span></button>
                <button type="button" class="tx-step" data-step-button="2" disabled><b>2</b><span>Opération</span></button>
                <button type="button" class="tx-step" data-step-button="3" disabled><b>3</b><span>Montant</span></button>
                <button type="button" class="tx-step" data-step-button="4" disabled><b>4</b><span>Références</span></button>
                <button type="button" class="tx-step" data-step-button="5" disabled><b>5</b><span>Résumé</span></button>
            </div>
        </div>
    </section>

    <form action="<?= url('transactions/store') ?>" method="post" id="transactionWizardForm">
        <?= csrfField() ?>
        <input type="hidden" name="id_service" id="id_service" required>
        <input type="hidden" name="id_type" id="id_type" required>

        <section class="app-card tx-panel active" data-step-panel="1">
            <div class="app-card-header">
                <span><i data-lucide="radio-tower"></i> Choisir un service</span>
                <span class="app-badge app-badge-secondary"><?= e(count($services)) ?> services</span>
            </div>
            <div class="app-card-body">
                <div class="tx-service-grid">
                    <?php foreach ($services as $service): ?>
                        <?php $logo = $serviceLogo($service['nom']); ?>
                        <button type="button" class="tx-service-card" data-service-id="<?= e($service['id_service']) ?>" data-service-name="<?= e($service['nom']) ?>" data-service-category="<?= e($service['categorie']) ?>">
                            <span class="tx-service-logo <?= e($logo['class']) ?>"><?= e($logo['label']) ?></span>
                            <strong><?= e($service['nom']) ?></strong>
                            <small><?= e($service['categorie']) ?></small>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="app-card tx-panel" data-step-panel="2">
            <div class="app-card-header">
                <span><i data-lucide="list-checks"></i> Type d'opération</span>
                <span class="app-badge app-badge-secondary" data-selected-service>Service non choisi</span>
            </div>
            <div class="app-card-body">
                <div class="tx-operation-grid" id="operationChoices"></div>
                <div class="bk-empty" id="emptyOperations" hidden>
                    <i data-lucide="inbox"></i>
                    <strong>Aucune opération configurée</strong>
                    <span>Ce service n'a pas encore de type d'opération actif dans la base.</span>
                </div>
                <div class="bk-actions justify-between mt-4">
                    <button type="button" class="bk-btn bk-btn-secondary" data-prev-step="1">
                        <i data-lucide="arrow-left"></i> Service
                    </button>
                </div>
            </div>
        </section>

        <section class="app-card tx-panel" data-step-panel="3">
            <div class="app-card-header">
                <span><i data-lucide="banknote"></i> Montant</span>
                <span class="app-badge app-badge-secondary" data-selected-operation>Opération non choisie</span>
            </div>
            <div class="app-card-body">
                <div class="tx-amount-wrap">
                    <label for="montant" class="bk-label">Montant de la transaction</label>
                    <input type="number" step="0.01" min="1" id="montant" name="montant" class="bk-field tx-amount-field" placeholder="0" required>
                    <span>FCFA</span>
                </div>
                <div class="bk-actions justify-between mt-4">
                    <button type="button" class="bk-btn bk-btn-secondary" data-prev-step="2">
                        <i data-lucide="arrow-left"></i> Opération
                    </button>
                    <button type="button" class="bk-btn bk-btn-primary" id="amountNext">
                        Continuer <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <section class="app-card tx-panel" data-step-panel="4">
            <div class="app-card-header">
                <span><i data-lucide="paperclip"></i> Références et annexes</span>
            </div>
            <div class="app-card-body">
                <div class="bk-form-grid">
                    <div>
                        <label for="reference" class="bk-label">Référence</label>
                        <input type="text" id="reference" name="reference" class="bk-field" maxlength="255" placeholder="Référence externe, numéro ou code">
                    </div>
                    <div>
                        <label for="note" class="bk-label">Annexes / note</label>
                        <textarea id="note" name="note" class="bk-field" rows="4" maxlength="1000" placeholder="Infos complémentaires, justificatif, remarque..."></textarea>
                    </div>
                </div>
                <div class="bk-actions justify-between mt-4">
                    <button type="button" class="bk-btn bk-btn-secondary" data-prev-step="3">
                        <i data-lucide="arrow-left"></i> Montant
                    </button>
                    <button type="button" class="bk-btn bk-btn-primary" id="referencesNext">
                        Voir le résumé <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <section class="app-card tx-panel" data-step-panel="5">
            <div class="app-card-header">
                <span><i data-lucide="clipboard-check"></i> Résumé</span>
            </div>
            <div class="app-card-body">
                <div class="tx-summary">
                    <div><span>Service</span><strong id="summaryService">—</strong></div>
                    <div><span>Opération</span><strong id="summaryOperation">—</strong></div>
                    <div><span>Montant</span><strong id="summaryAmount">—</strong></div>
                    <div><span>Référence</span><strong id="summaryReference">—</strong></div>
                    <div><span>Annexes</span><strong id="summaryNote">—</strong></div>
                </div>
                <div class="bk-actions justify-between mt-4">
                    <button type="button" class="bk-btn bk-btn-secondary" data-prev-step="4">
                        <i data-lucide="arrow-left"></i> Références
                    </button>
                    <button type="submit" class="bk-btn bk-btn-primary">
                        <i data-lucide="check-circle"></i> Enregistrer la transaction
                    </button>
                </div>
            </div>
        </section>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typesByService = <?= json_encode($typesByService ?? [], JSON_UNESCAPED_UNICODE) ?>;
        const form = document.getElementById('transactionWizardForm');
        const serviceInput = document.getElementById('id_service');
        const typeInput = document.getElementById('id_type');
        const amountInput = document.getElementById('montant');
        const referenceInput = document.getElementById('reference');
        const noteInput = document.getElementById('note');
        const operationChoices = document.getElementById('operationChoices');
        const emptyOperations = document.getElementById('emptyOperations');
        const selectedServiceBadges = document.querySelectorAll('[data-selected-service]');
        const selectedOperationBadges = document.querySelectorAll('[data-selected-operation]');
        const state = { step: 1, service: null, operation: null };

        const formatMoney = (value) => {
            const number = Number(value || 0);
            if (!number) return '—';
            return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(number) + ' FCFA';
        };

        const goToStep = (step) => {
            state.step = step;
            document.querySelectorAll('[data-step-panel]').forEach((panel) => {
                panel.classList.toggle('active', Number(panel.dataset.stepPanel) === step);
            });
            document.querySelectorAll('[data-step-button]').forEach((button) => {
                const current = Number(button.dataset.stepButton);
                button.classList.toggle('active', current === step);
                button.classList.toggle('done', current < step);
                button.disabled = current > maxUnlockedStep();
            });
            if (window.lucide) lucide.replace();
        };

        const maxUnlockedStep = () => {
            if (!state.service) return 1;
            if (!state.operation) return 2;
            if (!amountInput.value || Number(amountInput.value) <= 0) return 3;
            return 5;
        };

        const renderOperations = (serviceId) => {
            operationChoices.innerHTML = '';
            const operations = typesByService[serviceId] || [];
            emptyOperations.hidden = operations.length > 0;

            operations.forEach((operation) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'tx-operation-card';
                button.dataset.typeId = operation.id_type;
                button.dataset.typeName = operation.libelle;
                button.innerHTML = `
                    <span><i data-lucide="arrow-left-right"></i></span>
                    <strong>${escapeHtml(operation.libelle || 'Opération')}</strong>
                    <small>${operation.description ? escapeHtml(operation.description) : 'Type configuré en base'}</small>
                `;
                button.addEventListener('click', () => {
                    state.operation = operation;
                    typeInput.value = operation.id_type;
                    document.querySelectorAll('.tx-operation-card').forEach((card) => card.classList.remove('selected'));
                    button.classList.add('selected');
                    selectedOperationBadges.forEach((badge) => badge.textContent = operation.libelle);
                    goToStep(3);
                    amountInput.focus();
                });
                operationChoices.appendChild(button);
            });

            if (window.lucide) lucide.replace();
        };

        document.querySelectorAll('.tx-service-card').forEach((button) => {
            button.addEventListener('click', () => {
                const service = {
                    id: button.dataset.serviceId,
                    name: button.dataset.serviceName,
                    category: button.dataset.serviceCategory,
                };
                state.service = service;
                state.operation = null;
                serviceInput.value = service.id;
                typeInput.value = '';
                document.querySelectorAll('.tx-service-card').forEach((card) => card.classList.remove('selected'));
                button.classList.add('selected');
                selectedServiceBadges.forEach((badge) => badge.textContent = service.name);
                selectedOperationBadges.forEach((badge) => badge.textContent = 'Opération non choisie');
                renderOperations(service.id);
                goToStep(2);
            });
        });

        document.querySelectorAll('[data-prev-step]').forEach((button) => {
            button.addEventListener('click', () => goToStep(Number(button.dataset.prevStep)));
        });

        document.querySelectorAll('[data-step-button]').forEach((button) => {
            button.addEventListener('click', () => {
                const step = Number(button.dataset.stepButton);
                if (step <= maxUnlockedStep()) goToStep(step);
            });
        });

        document.getElementById('amountNext').addEventListener('click', () => {
            if (!amountInput.value || Number(amountInput.value) <= 0) {
                amountInput.focus();
                amountInput.reportValidity();
                return;
            }
            goToStep(4);
        });

        document.getElementById('referencesNext').addEventListener('click', () => {
            updateSummary();
            goToStep(5);
        });

        form.addEventListener('submit', (event) => {
            updateSummary();
            if (!serviceInput.value || !typeInput.value || !amountInput.value || Number(amountInput.value) <= 0) {
                event.preventDefault();
                goToStep(!serviceInput.value ? 1 : (!typeInput.value ? 2 : 3));
            }
        });

        const updateSummary = () => {
            document.getElementById('summaryService').textContent = state.service ? state.service.name : '—';
            document.getElementById('summaryOperation').textContent = state.operation ? state.operation.libelle : '—';
            document.getElementById('summaryAmount').textContent = formatMoney(amountInput.value);
            document.getElementById('summaryReference').textContent = referenceInput.value.trim() || '—';
            document.getElementById('summaryNote').textContent = noteInput.value.trim() || '—';
        };

        const escapeHtml = (value) => String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        goToStep(1);
    });
</script>
