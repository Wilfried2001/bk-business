// ============================================================
//  Interactions et fonctionnalités JavaScript
// ============================================================

document.addEventListener("DOMContentLoaded", function () {
  setupDismissButtons();
  setupDropdowns();
  setupCollapseToggles();

  // ── Préparer les animations au scroll ──
  document
    .querySelectorAll(".app-card, .app-stat-card")
    .forEach((el) => el.classList.add("animate-on-scroll"));
  highlightActivePage();
  observeElementsOnScroll();

  // ── Gestion du mode de calcul des commissions
  setupCommissionModeToggle();

  // ── Validations des formulaires ──
  setupFormValidation();

  // ── Initialiser les modals personnalisées ──
  setupModals();

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
  if (window.lucide) {
    lucide.replace();
  }
});

// Highlight la page active dans la sidebar
function normalizePath(path) {
  return path.replace(/\/+/g, "/").replace(/\/$/, "");
}

function highlightActivePage() {
  const currentPath = normalizePath(window.location.pathname);
  const sidebarLinks = document.querySelectorAll(".app-nav-link");

  sidebarLinks.forEach((link) => {
    const href = link.getAttribute("href");
    if (!href) return;
    const targetPath = normalizePath(
      new URL(href, window.location.origin).pathname,
    );
    if (currentPath === targetPath) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });
}

// Observe les éléments et les anime au scroll
function observeElementsOnScroll() {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("slide-in-up");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1 },
  );

  document.querySelectorAll(".app-card, .app-stat-card").forEach((el) => {
    observer.observe(el);
  });
}

// Validation des formulaires
function setupFormValidation() {
  const forms = document.querySelectorAll("form");

  forms.forEach((form) => {
    form.addEventListener(
      "submit",
      function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add("app-was-validated");
      },
      false,
    );
  });
}

// Gestion du mode de calcul des commissions
function setupCommissionModeToggle() {
  const commissionForms = document.querySelectorAll(
    'form[action$="/commissions/config"]',
  );

  commissionForms.forEach((form) => {
    const modeSelect = form.querySelector('select[name="mode_calcul"]');
    const valueGroup = form.querySelector(".commission-value-group");
    const trancheSection = form.querySelector(".commission-tranche-section");
    const trancheRows = form.querySelector(".tranche-rows");
    const addTrancheButton = form.querySelector(".add-tranche-row");

    if (
      !modeSelect ||
      !valueGroup ||
      !trancheSection ||
      !trancheRows ||
      !addTrancheButton
    )
      return;

    const createTrancheRow = (min = "", max = "", fixe = "") => {
      const row = document.createElement("div");
      row.className = "grid gap-3 md:grid-cols-4 tranche-row";
      row.innerHTML = `
        <div>
          <label class="app-label">Montant min</label>
          <input type="number" name="tranches[montant_min][]" class="app-field" step="0.01" value="${min}" required>
        </div>
        <div>
          <label class="app-label">Montant max</label>
          <input type="number" name="tranches[montant_max][]" class="app-field" step="0.01" value="${max}">
          <div class="app-help">Laisser vide pour plafond infini.</div>
        </div>
        <div>
          <label class="app-label">Commission fixe</label>
          <input type="number" name="tranches[montant_fixe][]" class="app-field" step="0.01" value="${fixe}" required>
        </div>
        <div class="flex items-end">
          <button type="button" class="app-btn app-btn-outline-danger w-full remove-tranche-row">
            <i data-lucide="trash"></i> Supprimer
          </button>
        </div>
      `;
      row.querySelector(".remove-tranche-row").addEventListener("click", () => row.remove());
      if (window.lucide) {
        lucide.replace();
      }
      return row;
    };

    const updateFormDisplay = () => {
      const mode = modeSelect.value;
      const valueInput = valueGroup.querySelector('input[name="valeur"]');
      const valueLabel = valueGroup.querySelector("label");

      if (mode === "TRANCHE") {
        valueGroup.style.display = "none";
        trancheSection.style.display = "block";
        if (valueInput) {
          valueInput.disabled = true;
          valueInput.required = false;
        }
        if (trancheRows.children.length === 0) {
          trancheRows.appendChild(createTrancheRow());
        }
      } else {
        valueGroup.style.display = "block";
        trancheSection.style.display = "none";
        if (valueInput) {
          valueInput.disabled = false;
          valueInput.required = true;
        }
      }

      if (valueLabel) {
        valueLabel.innerHTML =
          mode === "TAUX"
            ? '<i data-lucide="percent"></i> Taux (%)'
            : '<i data-lucide="dollar-sign"></i> Montant fixe';
        if (window.lucide) {
          lucide.replace();
        }
      }
    };

    addTrancheButton.addEventListener("click", () =>
      trancheRows.appendChild(createTrancheRow()),
    );
    modeSelect.addEventListener("change", updateFormDisplay);
    updateFormDisplay();
  });
}

// Confirmation avant suppression
function setupDeleteConfirmation() {
  const deleteButtons = document.querySelectorAll('[data-action="delete"]');

  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();

      const confirmed = confirm(
        "Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.",
      );

      if (confirmed) {
        const form = this.closest("form");
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

  moneyFields.forEach((field) => {
    if (field.tagName === "INPUT") {
      field.addEventListener("blur", function () {
        let value = parseFloat(this.value.replace(/[^\d.-]/g, ""));
        if (!isNaN(value)) {
          this.value = formatMoney(value);
        }
      });
    }
  });
}

// Formate un nombre en montant
function formatMoney(value) {
  return (
    new Intl.NumberFormat("fr-FR", {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(value) + " FCFA"
  );
}

// Ferme automatiquement les alertes après 5 secondes
function setupAutoCloseAlerts() {
  const alerts = document.querySelectorAll(".app-alert:not(.alert-permanent)");

  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.remove();
    }, 5000);
  });
}

// Affiche un toast de confirmation
function showToast(message, type = "success") {
  const toastHTML = `
        <div class="app-alert app-alert-${type}" role="status">
            <div>
                ${message}
            </div>
            <button type="button" class="app-close absolute right-3 top-3" data-dismiss="toast" aria-label="Fermer"></button>
        </div>
    `;

  const container =
    document.querySelector(".toast-container") ||
    document.body.appendChild(document.createElement("div"));
  container.classList.add("toast-container");

  const toastEl = document.createElement("div");
  toastEl.innerHTML = toastHTML;
  const toastNode = toastEl.firstElementChild;
  if (!toastNode) return;
  container.appendChild(toastNode);
  setupDismissButtons();
  setTimeout(() => toastNode.remove(), 5000);
}

// Initialiser les graphiques du dashboard s'ils existent
function initDashboardCharts() {
  if (typeof Chart === "undefined") return;

  try {
    const gridColor = "rgba(148, 163, 184, 0.18)";
    const tickColor = "#64748b";
    const moneyTooltip = (value) => formatMoney(Number(value || 0));

    const txCanvas = document.getElementById("transactionsChart");
    if (txCanvas) {
      const cfg = JSON.parse(txCanvas.getAttribute("data-chart") || "{}");
      new Chart(txCanvas, {
        type: "line",
        data: {
          labels: cfg.labels || [],
          datasets: [
            {
              label: "Transactions",
              data: cfg.data || [],
              borderColor: "#4f46e5",
              backgroundColor: "rgba(79, 70, 229, 0.09)",
              pointBackgroundColor: "#4f46e5",
              pointBorderColor: "#ffffff",
              pointRadius: 3,
              pointHoverRadius: 5,
              tension: 0.35,
              fill: true,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { mode: "index", intersect: false },
          plugins: { legend: { display: false } },
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: tickColor, maxTicksLimit: 6 },
            },
            y: {
              beginAtZero: true,
              grid: { color: gridColor },
              ticks: { color: tickColor, precision: 0 },
            },
          },
        },
      });
    }

    const commDailyCanvas = document.getElementById("commissionsDailyChart");
    if (commDailyCanvas) {
      const cfg = JSON.parse(commDailyCanvas.getAttribute("data-chart") || "{}");
      new Chart(commDailyCanvas, {
        type: "line",
        data: {
          labels: cfg.labels || [],
          datasets: [
            {
              label: "Commissions",
              data: cfg.data || [],
              borderColor: "#059669",
              backgroundColor: "rgba(5, 150, 105, 0.1)",
              pointBackgroundColor: "#059669",
              pointBorderColor: "#ffffff",
              pointRadius: 3,
              pointHoverRadius: 5,
              tension: 0.35,
              fill: true,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { mode: "index", intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: (context) => ` ${moneyTooltip(context.raw)}`,
              },
            },
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: tickColor, maxTicksLimit: 6 },
            },
            y: {
              beginAtZero: true,
              grid: { color: gridColor },
              ticks: {
                color: tickColor,
                callback: (value) => `${Math.round(Number(value) / 1000)}K`,
              },
            },
          },
        },
      });
    }

    const commCanvas = document.getElementById("commissionsChart");
    if (commCanvas) {
      const cfg = JSON.parse(commCanvas.getAttribute("data-chart") || "{}");
      new Chart(commCanvas, {
        type: "doughnut",
        data: {
          labels: cfg.labels || [],
          datasets: [
            {
              data: cfg.data || [],
              backgroundColor: [
                "#f97316",
                "#fbbf24",
                "#3b82f6",
                "#10b981",
                "#8b5cf6",
                "#06b6d4",
                "#ef4444",
              ],
              borderColor: "#ffffff",
              borderWidth: 3,
              hoverOffset: 5,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: "68%",
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: (context) => {
                  const label = context.label || "Service";
                  return ` ${label}: ${moneyTooltip(context.raw)}`;
                },
              },
            },
          },
        },
      });
    }
  } catch (e) {
    console.error("Erreur initialisation charts:", e);
  }
}

// Ajoute un bouton 'Détails' visible sur mobile et affiche le contenu de la ligne dans un modal
function setupRowDetails() {
  const modalEl = document.getElementById("rowDetailsModal");
  const contentEl = document.getElementById("rowDetailsContent");
  if (!modalEl || !contentEl) return;

  const openModal = () => {
    modalEl.classList.add("show");
    document.body.classList.add("modal-open");
  };

  const closeModal = () => {
    modalEl.classList.remove("show");
    document.body.classList.remove("modal-open");
  };

  document.querySelectorAll("table.table-mobile-details").forEach((table) => {
    const headers = Array.from(table.querySelectorAll("thead th")).map((th) =>
      th.innerText.trim(),
    );
    table.querySelectorAll("tbody tr").forEach((tr) => {
      const lastTd = tr.querySelector("td:last-child");
      if (!lastTd) return;

      // Avoid adding duplicate buttons
      if (lastTd.querySelector(".row-details-btn")) return;

      const btn = document.createElement("button");
      btn.type = "button";
      btn.className =
        "app-btn app-btn-sm app-btn-outline-primary lg:hidden ml-2 row-details-btn";
      btn.innerHTML = '<i data-lucide="list"></i> Détails';
      btn.addEventListener("click", () => {
        const cells = Array.from(tr.querySelectorAll("td"));
        const visibleCells = cells.slice(0, headers.length);
        let html = '<dl class="grid grid-cols-[9rem_1fr] gap-x-3 gap-y-2">';

        visibleCells.forEach((cell, index) => {
          const label = headers[index] || `Colonne ${index + 1}`;
          html += `<dt class="font-semibold text-slate-600">${eHtml(label)}</dt>`;
          html += `<dd>${eHtml(cell.innerText.trim())}</dd>`;
        });

        html += "</dl>";
        contentEl.innerHTML = html;
        openModal();
      });

      lastTd.appendChild(btn);
      if (window.lucide) {
        lucide.replace();
      }
    });
  });

  modalEl.querySelectorAll('[data-dismiss="modal"]').forEach((button) => {
    button.addEventListener("click", closeModal);
  });

  modalEl.addEventListener("click", (event) => {
    if (event.target === modalEl) {
      closeModal();
    }
  });

  function eHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  }
}

// Utilitaires pour tables avec actions
function setupTableActions() {
  const tables = document.querySelectorAll("table");

  tables.forEach((table) => {
    table.classList.add("app-table");
  });
}

function setupDismissButtons() {
  document.querySelectorAll('[data-dismiss="alert"]').forEach((button) => {
    button.addEventListener("click", () => {
      const alert = button.closest(".app-alert");
      if (alert) alert.remove();
    });
  });

  document.querySelectorAll('[data-dismiss="modal"]').forEach((button) => {
    button.addEventListener("click", () => {
      const modal = button.closest(".app-modal");
      if (modal) {
        modal.classList.remove("show");
        document.body.classList.remove("modal-open");
      }
    });
  });

  document.querySelectorAll('[data-dismiss="toast"]').forEach((button) => {
    button.addEventListener("click", () => {
      const toast = button.closest(".app-alert");
      if (toast) toast.remove();
    });
  });
}

function setupDropdowns() {
  document.querySelectorAll(".app-dropdown-toggle").forEach((toggle) => {
    const menu = toggle.nextElementSibling;
    if (!menu || !menu.classList.contains("app-dropdown-menu")) return;
    toggle.addEventListener("click", (event) => {
      event.preventDefault();
      menu.classList.toggle("show");
    });
  });

  document.addEventListener("click", (event) => {
    if (!event.target.closest(".app-dropdown")) {
      document.querySelectorAll(".app-dropdown-menu.show").forEach((menu) => {
        menu.classList.remove("show");
      });
    }
  });
}

function setupCollapseToggles() {
  document.querySelectorAll('[data-toggle="collapse"]').forEach((toggle) => {
    const targetSelector = toggle.getAttribute("data-target");
    if (!targetSelector) return;
    const target = document.querySelector(targetSelector);
    if (!target) return;
    toggle.addEventListener("click", (event) => {
      event.preventDefault();
      target.classList.toggle("show");
    });
  });
}

function setupModals() {
  document.querySelectorAll('[data-toggle="modal"]').forEach((button) => {
    const targetSelector = button.getAttribute("data-target");
    if (!targetSelector) return;
    const target = document.querySelector(targetSelector);
    if (!target) return;

    button.addEventListener("click", (event) => {
      event.preventDefault();
      openModal(target);
    });
  });

  document.querySelectorAll('[data-dismiss="modal"]').forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();
      const modal = button.closest(".app-modal");
      if (modal) closeModal(modal);
    });
  });

  function openModal(modal) {
    modal.classList.add("show");
    document.body.classList.add("modal-open");
  }

  function closeModal(modal) {
    modal.classList.remove("show");
    document.body.classList.remove("modal-open");
  }
}

// Toggle du menu mobile
function setupMobileMenu() {
  const toggleBtn = document.querySelector('[data-toggle="sidebar"]');
  const sidebar = document.querySelector(".app-sidebar");
  const closeBtn = document.querySelector(".close-sidebar");
  let backdrop = null;

  const createBackdrop = () => {
    backdrop = document.createElement("div");
    backdrop.className = "app-sidebar-backdrop";
    document.body.appendChild(backdrop);
    requestAnimationFrame(() => backdrop.classList.add("show"));
    backdrop.addEventListener("click", closeSidebar);
  };

  const closeSidebar = () => {
    if (!sidebar) return;
    sidebar.classList.remove("show");
    document.body.classList.remove("sidebar-open");
    if (backdrop) {
      backdrop.classList.remove("show");
      setTimeout(() => backdrop?.remove(), 300);
      backdrop = null;
    }
  };

  const openSidebar = () => {
    if (!sidebar) return;
    sidebar.classList.add("show");
    document.body.classList.add("sidebar-open");
    if (!backdrop) createBackdrop();
  };

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener("click", () => {
      if (sidebar.classList.contains("show")) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });

    const sidebarLinks = sidebar.querySelectorAll("a");
    sidebarLinks.forEach((link) => {
      link.addEventListener("click", () => {
        if (window.innerWidth < 992) {
          closeSidebar();
        }
      });
    });

    if (closeBtn) {
      closeBtn.addEventListener("click", closeSidebar);
    }

    window.addEventListener("resize", () => {
      if (window.innerWidth >= 992) {
        closeSidebar();
      }
    });
  }
}

// Smooth scroll pour les liens d'ancrage
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({ behavior: "smooth" });
    }
  });
});

// Export pour utilisation externe
window.AppUI = {
  showToast,
  formatMoney,
  highlightActivePage,
};
