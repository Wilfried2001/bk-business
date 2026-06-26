<?php
    $serviceLogo = function (string $name): array {
        $normalized = strtolower($name);
        if (str_contains($normalized, 'orange')) return ['label' => 'OM', 'class' => 'orange'];
        if (str_contains($normalized, 'mtn')) return ['label' => 'MTN', 'class' => 'mtn'];
        if (str_contains($normalized, 'western')) return ['label' => 'WU', 'class' => 'wu'];
        if (str_contains($normalized, 'moneygram')) return ['label' => 'MG', 'class' => 'mtn'];
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

    $isInternational = function (array $service): bool {
        $name = strtolower((string)($service['nom'] ?? ''));
        $category = strtoupper((string)($service['categorie'] ?? ''));
        return $category === 'INTERNATIONAL'
            || str_contains($name, 'ria')
            || str_contains($name, 'western')
            || str_contains($name, 'moneygram')
            || str_contains($name, 'cash');
    };
?>

<div class="bk-page tx-create-page">
    <section class="bk-head">
        <div>
            <p class="bk-eyebrow">Tableau de bord / Transactions / Nouvelle</p>
            <h1>Nouvelle transaction</h1>
            <p>Agence, service, opération, montant, motif et résumé avant validation.</p>
        </div>
        <div class="bk-actions">
            <a href="<?= url('transactions') ?>" class="bk-btn bk-btn-secondary">
                <i data-lucide="arrow-left"></i> Retour
            </a>
        </div>
    </section>

    <section class="app-card">
        <div class="app-card-body">
            <div class="tx-stepper tx-stepper-six" aria-label="Progression de la transaction">
                <button type="button" class="tx-step active" data-step-button="1"><b>1</b><span>Agence</span></button>
                <button type="button" class="tx-step" data-step-button="2" disabled><b>2</b><span>Service</span></button>
                <button type="button" class="tx-step" data-step-button="3" disabled><b>3</b><span>Opération</span></button>
                <button type="button" class="tx-step" data-step-button="4" disabled><b>4</b><span>Montant</span></button>
                <button type="button" class="tx-step" data-step-button="5" disabled><b>5</b><span>Infos</span></button>
                <button type="button" class="tx-step" data-step-button="6" disabled><b>6</b><span>Résumé</span></button>
            </div>
        </div>
    </section>

    <form action="<?= url('transactions/store') ?>" method="post" id="transactionWizardForm">
        <?= csrfField() ?>
        <input type="hidden" name="id_agence" id="id_agence" required>
        <input type="hidden" name="id_service" id="id_service" required>
        <input type="hidden" name="id_type" id="id_type" required>

        <section class="app-card tx-panel active" data-step-panel="1">
            <div class="app-card-header">
                <span><i data-lucide="building-2"></i> Choisir l'agence</span>
                <span class="app-badge app-badge-secondary"><?= e(count($agences ?? [])) ?> agences</span>
            </div>
            <div class="app-card-body">
                <?php if (empty($agences)): ?>
                    <div class="bk-empty">
                        <i data-lucide="building-2"></i>
                        <strong>Aucune agence active</strong>
                        <span>Ajoutez une agence active avant d'enregistrer une transaction.</span>
                    </div>
                <?php else: ?>
                    <div class="tx-service-grid">
                        <?php foreach ($agences as $agence): ?>
                            <button type="button" class="tx-service-card tx-agency-card" data-agence-id="<?= e($agence['id_agence']) ?>" data-agence-name="<?= e($agence['nom']) ?>">
                                <span class="tx-service-logo default"><?= e(strtoupper(substr($agence['code'] ?: $agence['nom'], 0, 3))) ?></span>
                                <strong><?= e($agence['nom']) ?></strong>
                                <small><?= e(trim(($agence['ville'] ?? '') . ' ' . ($agence['code'] ? '(' . $agence['code'] . ')' : ''))) ?></small>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="app-card tx-panel" data-step-panel="2">
            <div class="app-card-header">
                <span><i data-lucide="radio-tower"></i> Choisir un service</span>
                <span class="app-badge app-badge-secondary" data-selected-agence>Agence non choisie</span>
            </div>
            <div class="app-card-body">
                <div class="tx-service-grid">
                    <?php foreach ($services as $service): ?>
                        <?php $logo = $serviceLogo($service['nom']); ?>
                        <button type="button" class="tx-service-card" data-service-id="<?= e($service['id_service']) ?>" data-service-name="<?= e($service['nom']) ?>" data-service-category="<?= e($service['categorie']) ?>" data-service-international="<?= $isInternational($service) ? '1' : '0' ?>">
                            <span class="tx-service-logo <?= e($logo['class']) ?>"><?= e($logo['label']) ?></span>
                            <strong><?= e($service['nom']) ?></strong>
                            <small><?= e($service['categorie']) ?></small>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="bk-actions justify-between mt-4">
                    <button type="button" class="bk-btn bk-btn-secondary" data-prev-step="1">
                        <i data-lucide="arrow-left"></i> Agence
                    </button>
                </div>
            </div>
        </section>

        <section class="app-card tx-panel" data-step-panel="3">
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
                    <button type="button" class="bk-btn bk-btn-secondary" data-prev-step="2">
                        <i data-lucide="arrow-left"></i> Service
                    </button>
                </div>
            </div>
        </section>

        <section class="app-card tx-panel" data-step-panel="4">
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
                <div class="tx-summary mt-4">
                    <div><span>Nature automatique</span><strong id="autoNature">—</strong></div>
                    <div><span>Mouvement de solde</span><strong id="autoMovement">—</strong></div>
                </div>
                <div class="bk-actions justify-between mt-4">
                    <button type="button" class="bk-btn bk-btn-secondary" data-prev-step="3">
                        <i data-lucide="arrow-left"></i> Opération
                    </button>
                    <button type="button" class="bk-btn bk-btn-primary" id="amountNext">
                        Continuer <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <section class="app-card tx-panel" data-step-panel="5">
            <div class="app-card-header">
                <span><i data-lucide="paperclip"></i> Motif, références et parties</span>
                <span class="app-badge app-badge-secondary" id="partyModeBadge">Transaction standard</span>
            </div>
            <div class="app-card-body">
                <div class="bk-form-grid">
                    <div>
                        <label for="motif_transaction" class="bk-label">Motif de la transaction</label>
                        <input type="text" id="motif_transaction" name="motif_transaction" class="bk-field" maxlength="255" placeholder="Ex: Envoi fonds familial, paiement, annulation...">
                    </div>
                    <div>
                        <label for="reference" class="bk-label">Référence / code opération</label>
                        <input type="text" id="reference" name="reference" class="bk-field" maxlength="255" placeholder="Référence externe, numéro ou code">
                    </div>
                </div>

                <div class="tx-party-grid mt-4" data-international-fields>
                    <div class="app-card">
                        <div class="app-card-header"><span><i data-lucide="user-round"></i> Expéditeur</span></div>
                        <div class="app-card-body bk-page">
                            <div>
                                <label for="nom_expediteur" class="bk-label">Nom complet</label>
                                <input type="text" id="nom_expediteur" name="nom_expediteur" class="bk-field" maxlength="255">
                            </div>
                            <div>
                                <label for="expediteur_identifiant" class="bk-label">Numéro CNI</label>
                                <input type="text" id="expediteur_identifiant" name="expediteur_identifiant" class="bk-field" maxlength="100">
                            </div>
                            <div>
                                <label for="expediteur_telephone" class="bk-label">Téléphone</label>
                                <input type="tel" id="expediteur_telephone" name="expediteur_telephone" class="bk-field" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <div class="app-card">
                        <div class="app-card-header"><span><i data-lucide="user-check"></i> Bénéficiaire</span></div>
                        <div class="app-card-body bk-page">
                            <div>
                                <label for="nom_beneficiaire" class="bk-label">Nom complet</label>
                                <input type="text" id="nom_beneficiaire" name="nom_beneficiaire" class="bk-field" maxlength="255">
                            </div>
                            <div>
                                <label for="beneficiaire_identifiant" class="bk-label">Numéro CNI</label>
                                <input type="text" id="beneficiaire_identifiant" name="beneficiaire_identifiant" class="bk-field" maxlength="100">
                            </div>
                            <div>
                                <label for="beneficiaire_telephone" class="bk-label">Téléphone</label>
                                <input type="tel" id="beneficiaire_telephone" name="beneficiaire_telephone" class="bk-field" maxlength="50">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="note" class="bk-label">Annexes / note</label>
                    <textarea id="note" name="note" class="bk-field" rows="4" maxlength="1000" placeholder="Infos complémentaires, justificatif, remarque..."></textarea>
                </div>

                <div class="bk-actions justify-between mt-4">
                    <button type="button" class="bk-btn bk-btn-secondary" data-prev-step="4">
                        <i data-lucide="arrow-left"></i> Montant
                    </button>
                    <button type="button" class="bk-btn bk-btn-primary" id="referencesNext">
                        Voir le résumé <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <section class="app-card tx-panel" data-step-panel="6">
            <div class="app-card-header">
                <span><i data-lucide="clipboard-check"></i> Résumé</span>
            </div>
            <div class="app-card-body">
                <div class="tx-summary">
                    <div><span>Agence</span><strong id="summaryAgence">—</strong></div>
                    <div><span>Service</span><strong id="summaryService">—</strong></div>
                    <div><span>Opération</span><strong id="summaryOperation">—</strong></div>
                    <div><span>Nature</span><strong id="summaryNature">—</strong></div>
                    <div><span>Montant</span><strong id="summaryAmount">—</strong></div>
                    <div><span>Motif</span><strong id="summaryMotif">—</strong></div>
                    <div><span>Référence</span><strong id="summaryReference">—</strong></div>
                    <div><span>Expéditeur</span><strong id="summarySender">—</strong></div>
                    <div><span>Bénéficiaire</span><strong id="summaryBeneficiary">—</strong></div>
                    <div><span>Annexes</span><strong id="summaryNote">—</strong></div>
                </div>
                <div class="bk-actions justify-between mt-4">
                    <button type="button" class="bk-btn bk-btn-secondary" data-prev-step="5">
                        <i data-lucide="arrow-left"></i> Infos
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
        const agenceInput = document.getElementById('id_agence');
        const serviceInput = document.getElementById('id_service');
        const typeInput = document.getElementById('id_type');
        const amountInput = document.getElementById('montant');
        const motifInput = document.getElementById('motif_transaction');
        const referenceInput = document.getElementById('reference');
        const noteInput = document.getElementById('note');
        const operationChoices = document.getElementById('operationChoices');
        const emptyOperations = document.getElementById('emptyOperations');
        const selectedAgenceBadges = document.querySelectorAll('[data-selected-agence]');
        const selectedServiceBadges = document.querySelectorAll('[data-selected-service]');
        const selectedOperationBadges = document.querySelectorAll('[data-selected-operation]');
        const internationalFields = document.querySelector('[data-international-fields]');
        const partyModeBadge = document.getElementById('partyModeBadge');
        const partyInputs = Array.from(internationalFields.querySelectorAll('input'));
        const state = { step: 1, agence: null, service: null, operation: null };

        const formatMoney = (value) => {
            const number = Number(value || 0);
            if (!number) return '—';
            return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(number) + ' FCFA';
        };

        const movementFor = (operation) => {
            const impactCaisse = Number(operation?.impact_caisse || 0);
            const impactFloat = Number(operation?.impact_float || 0);
            const principal = impactCaisse !== 0 ? impactCaisse : impactFloat;
            if (principal > 0) return { type: 'ENTREE', label: impactCaisse !== 0 ? 'Entrée en caisse' : 'Entrée en float' };
            if (principal < 0) return { type: 'SORTIE', label: impactCaisse !== 0 ? 'Sortie de caisse' : 'Sortie de float' };
            return { type: 'NEUTRE', label: 'Aucun impact direct' };
        };

        const maxUnlockedStep = () => {
            if (!state.agence) return 1;
            if (!state.service) return 2;
            if (!state.operation) return 3;
            if (!amountInput.value || Number(amountInput.value) <= 0) return 4;
            return 6;
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

        const renderOperations = (serviceId) => {
            operationChoices.innerHTML = '';
            const operations = typesByService[serviceId] || [];
            emptyOperations.hidden = operations.length > 0;

            operations.forEach((operation) => {
                const movement = movementFor(operation);
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'tx-operation-card';
                button.innerHTML = `
                    <span><i data-lucide="arrow-left-right"></i></span>
                    <strong>${escapeHtml(operation.libelle || 'Opération')}</strong>
                    <small>${escapeHtml(movement.label)}</small>
                `;
                button.addEventListener('click', () => {
                    state.operation = operation;
                    typeInput.value = operation.id_type;
                    document.querySelectorAll('.tx-operation-card').forEach((card) => card.classList.remove('selected'));
                    button.classList.add('selected');
                    selectedOperationBadges.forEach((badge) => badge.textContent = operation.libelle);
                    document.getElementById('autoNature').textContent = movement.type;
                    document.getElementById('autoMovement').textContent = movement.label;
                    goToStep(4);
                    amountInput.focus();
                });
                operationChoices.appendChild(button);
            });

            if (window.lucide) lucide.replace();
        };

        const updateInternationalMode = () => {
            const isInternational = state.service?.international === '1';
            internationalFields.hidden = !isInternational;
            partyModeBadge.textContent = isInternational ? 'Transaction internationale' : 'Transaction standard';
            motifInput.required = isInternational;
            partyInputs.forEach((input) => input.required = isInternational);
        };

        document.querySelectorAll('.tx-agency-card').forEach((button) => {
            button.addEventListener('click', () => {
                state.agence = { id: button.dataset.agenceId, name: button.dataset.agenceName };
                agenceInput.value = state.agence.id;
                document.querySelectorAll('.tx-agency-card').forEach((card) => card.classList.remove('selected'));
                button.classList.add('selected');
                selectedAgenceBadges.forEach((badge) => badge.textContent = state.agence.name);
                goToStep(2);
            });
        });

        document.querySelectorAll('.tx-service-card:not(.tx-agency-card)').forEach((button) => {
            button.addEventListener('click', () => {
                state.service = {
                    id: button.dataset.serviceId,
                    name: button.dataset.serviceName,
                    category: button.dataset.serviceCategory,
                    international: button.dataset.serviceInternational,
                };
                state.operation = null;
                serviceInput.value = state.service.id;
                typeInput.value = '';
                document.querySelectorAll('.tx-service-card:not(.tx-agency-card)').forEach((card) => card.classList.remove('selected'));
                button.classList.add('selected');
                selectedServiceBadges.forEach((badge) => badge.textContent = state.service.name);
                selectedOperationBadges.forEach((badge) => badge.textContent = 'Opération non choisie');
                updateInternationalMode();
                renderOperations(state.service.id);
                goToStep(3);
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
            goToStep(5);
        });

        document.getElementById('referencesNext').addEventListener('click', () => {
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            updateSummary();
            goToStep(6);
        });

        form.addEventListener('submit', (event) => {
            updateSummary();
            if (!form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
            }
        });

        const updateSummary = () => {
            const movement = movementFor(state.operation);
            document.getElementById('summaryAgence').textContent = state.agence ? state.agence.name : '—';
            document.getElementById('summaryService').textContent = state.service ? state.service.name : '—';
            document.getElementById('summaryOperation').textContent = state.operation ? state.operation.libelle : '—';
            document.getElementById('summaryNature').textContent = movement.type + ' · ' + movement.label;
            document.getElementById('summaryAmount').textContent = formatMoney(amountInput.value);
            document.getElementById('summaryMotif').textContent = motifInput.value.trim() || '—';
            document.getElementById('summaryReference').textContent = referenceInput.value.trim() || '—';
            document.getElementById('summarySender').textContent = partyInputs[0].value.trim() || '—';
            document.getElementById('summaryBeneficiary').textContent = partyInputs[3].value.trim() || '—';
            document.getElementById('summaryNote').textContent = noteInput.value.trim() || '—';
        };

        const escapeHtml = (value) => String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        updateInternationalMode();
        goToStep(1);
    });
</script>
