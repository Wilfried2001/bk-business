<div class="app-shell">
        <aside class="app-sidebar py-4">
            <div class="sidebar-brand">
                <a href="<?= url('dashboard') ?>" class="brand-mark">
                    <span>B</span>
                    <strong>BK Business</strong>
                </a>
                <button class="icon-btn lg:hidden close-sidebar" type="button" aria-label="Fermer le menu">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-group">
                    <p>Principal</p>
                    <a class="app-nav-link" href="<?= url('dashboard') ?>">
                        <i data-lucide="layout-dashboard"></i> Tableau de bord
                    </a>
                    <?php if (Auth::hasRole(['AGENT', 'SUPERVISEUR', 'DG'])): ?>
                    <a class="app-nav-link" href="<?= url('transactions') ?>">
                        <i data-lucide="arrow-left-right"></i> Transactions
                    </a>
                    <a class="app-nav-link" href="<?= url('transactions/create') ?>">
                        <i data-lucide="circle-plus"></i> Nouvelle transaction
                    </a>
                    <?php endif; ?>
                </div>

                <?php if (Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
                <div class="nav-group">
                    <p>Gestion</p>
                    <a class="app-nav-link" href="<?= url('stocks') ?>">
                        <i data-lucide="wallet-cards"></i> Stocks & Soldes
                    </a>
                    <a class="app-nav-link" href="<?= url('alertes') ?>">
                        <i data-lucide="bell-ring"></i> Alertes
                    </a>
                    <a class="app-nav-link" href="<?= url('presences') ?>">
                        <i data-lucide="calendar-check-2"></i> Présences
                    </a>
                </div>
                <?php endif; ?>

                <?php if (Auth::hasRole(['COMPTABLE', 'DG'])): ?>
                <div class="nav-group">
                    <p>Finance</p>
                    <a class="app-nav-link" href="<?= url('commissions') ?>">
                        <i data-lucide="badge-percent"></i> Commissions
                    </a>
                    <a class="app-nav-link" href="<?= url('commissions/config') ?>">
                        <i data-lucide="sliders-horizontal"></i> Paramétrage
                    </a>
                </div>
                <?php endif; ?>

                <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                <div class="nav-group">
                    <p>Rapports</p>
                    <a class="app-nav-link" href="<?= url('rapports') ?>">
                        <i data-lucide="bar-chart-3"></i> Rapports
                    </a>
                    <?php if (Auth::hasRole(['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                    <a class="app-nav-link" href="<?= url('agent') ?>">
                        <i data-lucide="bot"></i> Agent IA
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (Auth::hasRole(['DG'])): ?>
                <div class="nav-group">
                    <p>Administration</p>
                    <a class="app-nav-link" href="<?= url('utilisateurs') ?>">
                        <i data-lucide="users"></i> Utilisateurs
                    </a>
                </div>
                <?php endif; ?>
            </nav>

            <?php
                $health = $sidebarHealth ?? [
                    'score' => 0,
                    'statut' => 'Indisponible',
                    'couleur' => '#64748b',
                    'nb_alertes' => 0,
                    'disponibilite_soldes' => 0,
                    'nb_transactions_jour' => 0,
                ];
                $healthTitle = sprintf(
                    'Alertes actives: %d | Disponibilite des soldes: %d%% | Transactions du jour: %d',
                    (int)$health['nb_alertes'],
                    (int)$health['disponibilite_soldes'],
                    (int)$health['nb_transactions_jour']
                );
            ?>
            <div class="network-health" title="<?= e($healthTitle) ?>">
                <div>
                    <strong>Santé opérationnelle</strong>
                    <span>Réseau : <?= e($health['statut']) ?></span>
                </div>
                <b><?= e((int)$health['score']) ?>%</b>
                <div class="health-bar">
                    <span style="width: <?= e((int)$health['score']) ?>%; background-color: <?= e($health['couleur']) ?>"></span>
                </div>
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
