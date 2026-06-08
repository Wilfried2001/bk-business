// ============================================================
//  Interactions et fonctionnalités JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    // ── Activer les tooltips Bootstrap ──
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ── Activer les popovers Bootstrap ──
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // ── Préparer les animations au scroll ──
    document.querySelectorAll('.card, .stat-card').forEach(el => el.classList.add('animate-on-scroll'));
    highlightActivePage();
    observeElementsOnScroll();

    // ── Gestion du mode de calcul des commissions
    setupCommissionModeToggle();

    // ── Validations des formulaires ──
    setupFormValidation();

    // ── Confirmation avant suppression ──
    setupDeleteConfirmation();

    // ── Format des montants ──
    formatMoneyFields();
    // ── Activer les tables interactives ──
    setupTableActions();

    // ── Gérer le menu mobile ──
    setupMobileMenu();
    // ── Toast automatique ──
    setupAutoCloseAlerts();
    // ── Initialiser graphiques du dashboard ──
    initDashboardCharts();
    // ── Préparer les détails de lignes pour mobile ──
    setupRowDetails();
});

// Highlight la page active dans la sidebar
function normalizePath(path) {
    return path.replace(/\/+/g, '/').replace(/\/$/, '');
}

function highlightActivePage() {
    const currentPath = normalizePath(window.location.pathname);
    const sidebarLinks = document.querySelectorAll('.list-group-item');

    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;
        const targetPath = normalizePath(new URL(href, window.location.origin).pathname);
        if (currentPath === targetPath) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

// Observe les éléments et les anime au scroll
function observeElementsOnScroll() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('slide-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.card, .stat-card').forEach(el => {
        observer.observe(el);
    });
}

// Validation des formulaires Bootstrap
function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

// Gestion du mode de calcul des commissions
function setupCommissionModeToggle() {
    const commissionForms = document.querySelectorAll('form[action$="/commissions/config"]');

    commissionForms.forEach(form => {
        const modeSelect = form.querySelector('select[name="mode_calcul"]');
        const valueGroup = form.querySelector('.commission-value-group');
        const trancheSection = form.querySelector('.commission-tranche-section');
        const trancheRows = form.querySelector('.tranche-rows');
        const addTrancheButton = form.querySelector('.add-tranche-row');

        if (!modeSelect || !valueGroup || !trancheSection || !trancheRows || !addTrancheButton) return;

        const createTrancheRow = (min = '', max = '', fixe = '') => {
            const row = document.createElement('div');
            row.className = 'row g-3 mb-2 tranche-row';
            row.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label">Montant min</label>
                    <input type="number" name="tranches[montant_min][]" class="form-control" step="0.01" value="${min}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Montant max</label>
                    <input type="number" name="tranches[montant_max][]" class="form-control" step="0.01" value="${max}">
                    <div class="form-text">Laisser vide pour plafond infini.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Commission fixe</label>
                    <input type="number" name="tranches[montant_fixe][]" class="form-control" step="0.01" value="${fixe}" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger remove-tranche-row w-100">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </div>
            `;

            const removeButton = row.querySelector('.remove-tranche-row');
            if (removeButton) {
                removeButton.addEventListener('click', () => row.remove());
            }
            return row;
        };

        const updateFormDisplay = () => {
            const mode = modeSelect.value;
            const valueLabel = valueGroup.querySelector('label');
            const valueInput = valueGroup.querySelector('input[name="valeur"]');

            if (mode === 'TRANCHE') {
                valueGroup.style.display = 'none';
                trancheSection.style.display = 'block';
                if (valueInput) {
                    valueInput.disabled = true;
                    valueInput.required = false;
                }
                if (trancheRows.children.length === 0) {
                    trancheRows.appendChild(createTrancheRow());
                }
            } else {
                valueGroup.style.display = 'block';
                trancheSection.style.display = 'none';
                if (valueInput) {
                    valueInput.disabled = false;
                    valueInput.required = true;
                }
            }

            if (valueLabel) {
                valueLabel.innerHTML = mode === 'TAUX'
                    ? '<i class="bi bi-percent"></i> Taux (%)'
                    : '<i class="bi bi-currency-dollar"></i> Montant fixe';
            }
        };

        addTrancheButton.addEventListener('click', () => trancheRows.appendChild(createTrancheRow()));
        modeSelect.addEventListener('change', updateFormDisplay);
        updateFormDisplay();
    });
}

// Confirmation avant suppression
function setupDeleteConfirmation() {
    const deleteButtons = document.querySelectorAll('[data-action="delete"]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const confirmed = confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.');
            
            if (confirmed) {
                const form = this.closest('form');
                if (form) {
                    form.submit();
                } else {
                    window.location.href = this.href;
                }
            }
        });
    });
}

// Format les champs montants
function formatMoneyFields() {
    const moneyFields = document.querySelectorAll('[data-type="money"]');
    
    moneyFields.forEach(field => {
        if (field.tagName === 'INPUT') {
            field.addEventListener('blur', function() {
                let value = parseFloat(this.value.replace(/[^\d.-]/g, ''));
                if (!isNaN(value)) {
                    this.value = formatMoney(value);
                }
            });
        }
    });
}

// Formate un nombre en montant
function formatMoney(value) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value) + ' FCFA';
}

// Ferme automatiquement les alertes après 5 secondes
function setupAutoCloseAlerts() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

// Affiche un toast de confirmation
function showToast(message, type = 'success') {
    const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    const container = document.querySelector('.toast-container') || 
                     document.body.appendChild(document.createElement('div'));
    container.classList.add('toast-container', 'position-fixed', 'top-0', 'end-0', 'p-3');
    
    const toastEl = document.createElement('div');
    toastEl.innerHTML = toastHTML;
    container.appendChild(toastEl);
    
    const toast = new bootstrap.Toast(toastEl.querySelector('.toast'));
    toast.show();
    
    setTimeout(() => toastEl.remove(), 5000);
}

// Initialiser les graphiques du dashboard s'ils existent
function initDashboardCharts() {
    if (typeof Chart === 'undefined') return;

    try {
        const txCanvas = document.getElementById('transactionsChart');
        if (txCanvas) {
            const cfg = JSON.parse(txCanvas.getAttribute('data-chart') || '{}');
            new Chart(txCanvas, {
                type: 'line',
                data: {
                    labels: cfg.labels || [],
                    datasets: [{
                        label: 'Transactions',
                        data: cfg.data || [],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.08)',
                        tension: 0.25,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }

        const commCanvas = document.getElementById('commissionsChart');
        if (commCanvas) {
            const cfg = JSON.parse(commCanvas.getAttribute('data-chart') || '{}');
            new Chart(commCanvas, {
                type: 'doughnut',
                data: {
                    labels: cfg.labels || [],
                    datasets: [{
                        data: cfg.data || [],
                        backgroundColor: [
                            '#0d6efd','#198754','#ffc107','#dc3545','#6610f2','#0dcaf0','#fd7e14'
                        ],
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    } catch (e) {
        console.error('Erreur initialisation charts:', e);
    }
}

// Ajoute un bouton 'Détails' visible sur mobile et affiche le contenu de la ligne dans un modal
function setupRowDetails() {
    const modalEl = document.getElementById('rowDetailsModal');
    const contentEl = document.getElementById('rowDetailsContent');
    if (!modalEl || !contentEl) return;
    const modal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('table.table-mobile-details').forEach(table => {
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText.trim());
        table.querySelectorAll('tbody tr').forEach(tr => {
            const lastTd = tr.querySelector('td:last-child');
            if (!lastTd) return;

            // Avoid adding duplicate buttons
            if (lastTd.querySelector('.row-details-btn')) return;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-primary d-lg-none ms-2 row-details-btn';
            btn.textContent = 'Détails';
            btn.addEventListener('click', () => {
                // Use data attributes when available for clearer values
                const ds = tr.dataset || {};
                const idService = ds.idService || tr.getAttribute('data-id-service') || '';
                const idSolde = ds.idSolde || tr.getAttribute('data-id-solde') || '';
                const nomService = ds.nomService || tr.getAttribute('data-nom-service') || '';
                const categorie = ds.categorie || tr.getAttribute('data-categorie') || '';
                const typeSolde = ds.typeSolde || tr.getAttribute('data-type-solde') || '';
                const montantDisplay = ds.montantDisplay || tr.getAttribute('data-montant-display') || ''; 
                const montantVal = ds.montantVal || tr.getAttribute('data-montant-val') || '';
                const seuilDisplay = ds.seuilDisplay || tr.getAttribute('data-seuil-display') || '';
                const seuilVal = ds.seuilVal || tr.getAttribute('data-seuil-val') || '';
                const enAlerte = ds.enAlerte || tr.getAttribute('data-en-alerte') || '0';

                let html = '<dl class="row">';
                html += `<dt class="col-5 fw-bold">Service</dt><dd class="col-7">${eHtml(nomService)}</dd>`;
                html += `<dt class="col-5 fw-bold">Catégorie</dt><dd class="col-7">${eHtml(categorie)}</dd>`;
                html += `<dt class="col-5 fw-bold">Type</dt><dd class="col-7">${eHtml(typeSolde)}</dd>`;
                html += `<dt class="col-5 fw-bold">Montant</dt><dd class="col-7">${eHtml(montantDisplay)}</dd>`;
                html += `<dt class="col-5 fw-bold">Seuil</dt><dd class="col-7">${eHtml(seuilDisplay)}</dd>`;
                html += `<dt class="col-5 fw-bold">Statut</dt><dd class="col-7">${enAlerte === '1' ? '<span class="badge bg-danger">Alerte</span>' : '<span class="badge bg-success">Normal</span>'}</dd>`;
                html += '</dl>';

                // If user can edit, append quick edit form
                if (window.canEditSeuil) {
                    const action = (window.baseUrl || '') + '/stocks/' + idService + '/seuil';
                    const valeur = seuilVal !== undefined && seuilVal !== '' ? seuilVal : '';
                    html += `
                        <form action="${eHtml(action)}" method="post" class="mt-3">
                            <input type="hidden" name="csrf_token" value="${eHtml(window.csrfToken || '')}">
                            <input type="hidden" name="id_solde" value="${eHtml(idSolde)}">
                            <input type="hidden" name="redirect_to" value="stocks">
                            <div class="input-group">
                                <input type="number" name="valeur_seuil" step="0.01" class="form-control form-control-sm" value="${eHtml(valeur)}" placeholder="Seuil" required>
                                <button class="btn btn-primary btn-sm" type="submit">Enregistrer</button>
                            </div>
                        </form>
                    `;
                }

                contentEl.innerHTML = html;
                modal.show();
            });

            lastTd.appendChild(btn);
        });
    });

    function eHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
}

// Utilitaires pour tables avec actions
function setupTableActions() {
    const tables = document.querySelectorAll('table');
    
    tables.forEach(table => {
        table.classList.add('table-hover');
    });
}

// Toggle du menu mobile
function setupMobileMenu() {
    const toggleBtn = document.querySelector('[data-toggle="sidebar"]');
    const sidebar = document.querySelector('.sidebar');
    const closeBtn = document.querySelector('.close-sidebar');
    let backdrop = null;

    const createBackdrop = () => {
        backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
        requestAnimationFrame(() => backdrop.classList.add('show'));
        backdrop.addEventListener('click', closeSidebar);
    };

    const closeSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.remove('show');
        document.body.classList.remove('sidebar-open');
        if (backdrop) {
            backdrop.classList.remove('show');
            setTimeout(() => backdrop?.remove(), 300);
            backdrop = null;
        }
    };

    const openSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.add('show');
        document.body.classList.add('sidebar-open');
        if (!backdrop) createBackdrop();
    };

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            if (sidebar.classList.contains('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });
    }
}

// Smooth scroll pour les liens d'ancrage
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// Export pour utilisation externe
window.AppUI = {
    showToast,
    formatMoney,
    highlightActivePage
};
