<div class="app-shell">
        <aside class="app-sidebar py-4">
            <div class="mb-4 flex items-center justify-between px-4">
                <h5 class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-primary"><i data-lucide="compass"></i> Navigation</h5>
                <button class="app-btn app-btn-sm app-btn-secondary lg:hidden close-sidebar" type="button"
                    aria-label="Fermer le menu">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <div>
                <a class="app-nav-link" href="<?= url('dashboard') ?>">
                    <i data-lucide="gauge"></i> Tableau de bord
                </a>
                <?php if (Auth::hasRole(['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                <a class="app-nav-link" href="<?= url('agent') ?>">
                    <i data-lucide="bot"></i> Agent IA
                </a>
                <?php endif; ?>
                <?php if (Auth::hasRole(['AGENT', 'SUPERVISEUR', 'DG'])): ?>
                <a class="app-nav-link" href="<?= url('transactions') ?>">
                    <i data-lucide="arrow-left-right"></i> Transactions
                </a>
                <?php endif; ?>
                <?php if (Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
                <a class="app-nav-link" href="<?= url('stocks') ?>">
                    <i data-lucide="package"></i> Stocks
                </a>
                <a class="app-nav-link" href="<?= url('alertes') ?>">
                    <i data-lucide="alert-triangle"></i> Alertes
                </a>
                <?php endif; ?>
                <?php if (Auth::hasRole(['COMPTABLE', 'DG'])): ?>
                <a class="app-nav-link" href="<?= url('commissions') ?>">
                    <i data-lucide="percent"></i> Commissions
                </a>
                <a class="app-nav-link" href="<?= url('commissions/config') ?>">
                    <i data-lucide="settings"></i> Paramétrage commissions
                </a>
                <?php endif; ?>
                <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                <a class="app-nav-link" href="<?= url('rapports') ?>">
                    <i data-lucide="bar-chart"></i> Rapports
                </a>

                <?php endif; ?>
                <?php if (Auth::hasRole(['DG'])): ?>
                <a class="app-nav-link" href="<?= url('utilisateurs') ?>">
                    <i data-lucide="users"></i> Utilisateurs
                </a>
                <?php endif; ?>
            </div>
        </aside>
        <main class="app-main">
            <?php if ($success = Session::getFlash('success')): ?>
            <div class="app-alert app-alert-success app-alert-dismissible" role="alert">
                <?= e($success) ?>
                <button type="button" class="app-close absolute right-3 top-3" data-dismiss="alert" aria-label="Fermer"></button>
            </div>
            <?php endif; ?>
            <?php if ($error = Session::getFlash('error')): ?>
            <div class="app-alert app-alert-danger app-alert-dismissible" role="alert">
                <?= e($error) ?>
                <button type="button" class="app-close absolute right-3 top-3" data-dismiss="alert" aria-label="Fermer"></button>
            </div>
            <?php endif; ?>
