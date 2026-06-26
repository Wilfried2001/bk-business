<nav class="app-topbar">
    <div class="topbar-left">
        <button class="icon-btn lg:hidden" type="button" data-toggle="sidebar" aria-label="Ouvrir le menu">
            <i data-lucide="menu"></i>
        </button>
        <label class="topbar-search">
            <i data-lucide="search"></i>
            <input type="search" placeholder="Rechercher..." aria-label="Rechercher">
        </label>
    </div>

    <div class="topbar-right">
        <span class="topbar-date"><i data-lucide="calendar-days"></i> <?= e(date('d M Y')) ?></span>
        <?php if (Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
            <a class="icon-btn notification-dot" href="<?= url('alertes') ?>" aria-label="Voir les alertes">
                <i data-lucide="bell"></i>
            </a>
        <?php endif; ?>
        <?php if (Auth::hasRole(['AGENT', 'SUPERVISEUR', 'DG'])): ?>
            <a class="app-btn app-btn-primary topbar-action" href="<?= url('transactions/create') ?>">
                <i data-lucide="plus"></i> Nouvelle transaction
            </a>
        <?php endif; ?>
        <?php if (Auth::check()): ?>
            <div class="app-dropdown">
                <a class="user-menu app-dropdown-toggle" href="#" id="userDropdown" role="button" aria-expanded="false">
                    <span><?= e(strtoupper(substr(Auth::nom() ?: Auth::role(), 0, 1))) ?></span>
                    <strong><?= e(Auth::role() ?: 'Utilisateur') ?></strong>
                    <i data-lucide="chevron-down"></i>
                </a>
                <div class="app-dropdown-menu" aria-labelledby="userDropdown">
                    <a class="app-dropdown-item" href="<?= url('dashboard') ?>"><i data-lucide="user-circle"></i> Profil</a>
                    <?php if (Auth::hasRole(['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                        <a class="app-dropdown-item" href="<?= url('agent') ?>"><i data-lucide="bot"></i> Agent IA</a>
                    <?php endif; ?>
                    <div class="my-2 h-px bg-slate-100"></div>
                    <a class="app-dropdown-item text-danger" href="<?= url('auth/logout') ?>">
                        <i data-lucide="log-out"></i> Déconnexion
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav>
