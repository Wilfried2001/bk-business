<nav class="app-topbar">
    <div class="flex w-full items-center justify-between gap-4">
        <div class="flex items-center gap-3">
        <button class="app-btn app-btn-sm border border-white/60 text-white hover:bg-white/10 lg:hidden" type="button" data-toggle="sidebar" aria-label="Ouvrir le menu">
            <i data-lucide="menu"></i>
        </button>
        <a class="app-brand" href="<?= url('dashboard') ?>">
            <i data-lucide="briefcase"></i> <?= e(APP_NAME) ?>
        </a>
        </div>
        <div class="flex items-center gap-3">
            <?php if (Auth::hasRole(['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                <a class="app-btn app-btn-sm border border-white/60 text-white hover:bg-white/10" href="<?= url('agent') ?>">
                    <i data-lucide="bot"></i> Agent IA
                </a>
            <?php endif; ?>
            <?php if (Auth::check()): ?>
                <div class="app-dropdown">
                    <a class="app-btn app-btn-sm text-white hover:bg-white/10 app-dropdown-toggle" href="#" id="userDropdown" role="button" aria-expanded="false">
                        <i data-lucide="user-circle"></i> <?= e(Auth::nom()) ?>
                    </a>
                    <div class="app-dropdown-menu" aria-labelledby="userDropdown">
                        <a class="app-dropdown-item" href="<?= url('dashboard') ?>"><i data-lucide="gauge"></i> Profil</a>
                        <div class="my-2 h-px bg-slate-100"></div>
                        <a class="app-dropdown-item text-danger" href="<?= url('auth/logout') ?>">
                            <i data-lucide="log-out"></i> Déconnexion
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
