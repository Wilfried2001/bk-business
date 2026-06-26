<?php if (isset($userRoles) && !isset($utilisateurs) && !isset($utilisateur)): ?>

<!-- Créer un utilisateur -->
<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="user-plus"></i> Créer un utilisateur</h1>
        <p class="text-gray-500">Ajouter un nouveau compte utilisateur à l'application.</p>
    </div>
    <a href="<?= url('utilisateurs') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-[#0d47a1] text-[#0d47a1] bg-transparent hover:bg-[#eef4ff]">
        <i data-lucide="arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="app-card shadow-sm mb-4">
    <div class="app-card-header">
        <strong>Informations utilisateur</strong>
    </div>
    <div class="app-card-body">
        <form action="<?= url('utilisateurs/store') ?>" method="post">
            <?= csrfField() ?>
            <div class="flex flex-wrap -mx-3 gy-3">
                <div class="md:w-1/2 px-3">
                    <label for="nom" class="app-label"><i data-lucide="user"></i> Nom complet</label>
                    <input type="text" id="nom" name="nom" class="border rounded-md px-3 py-2 text-sm w-full" placeholder="Ex: Jean Dupont" required>
                <div class="md:w-1/2 px-3">
                    <label for="email" class="app-label"><i data-lucide="mail"></i> Adresse email</label>
                    <input type="email" id="email" name="email" class="border rounded-md px-3 py-2 text-sm w-full" placeholder="Ex: jean@example.com"
                        required>
                </div>
                <div class="md:w-1/3 px-3">
                    <label for="role" class="app-label"><i data-lucide="shield"></i> Rôle</label>
                    <select id="role" name="role" class="border rounded-md px-3 py-2 text-sm w-full bg-white" required>
                        <option value="">Sélectionner un rôle</option>
                        <?php foreach ($userRoles as $role): ?>
                        <option value="<?= e($role) ?>"><?= e($role) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:w-1/3 px-3">
                    <label for="mot_de_passe" class="app-label"><i data-lucide="key"></i> Mot de passe
                        temporaire</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" class="border rounded-md px-3 py-2 text-sm w-full"
                        placeholder="Mot de passe initial" required>
                </div>
                <div class="md:w-1/3 px-3 flex items-end">
                    <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white w-full">
                        <i data-lucide="check-circle"></i> Créer l'utilisateur
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php elseif (isset($utilisateur)): ?>

<!-- Modifier un utilisateur -->
<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="edit-2"></i> Modifier l'utilisateur</h1>
        <p class="text-gray-500">Mettre à jour les informations du compte.</p>
    </div>
    <a href="<?= url('utilisateurs') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
        <i data-lucide="arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="app-card shadow-sm mb-4">
    <div class="app-card-header">
        <strong>Détails du compte</strong>
    </div>
    <div class="app-card-body">
        <form action="<?= url('utilisateurs/' . (int)$utilisateur['id_user'] . '/edit') ?>" method="post">
            <?= csrfField() ?>
            <div class="flex flex-wrap -mx-3 gy-3">
                <div class="md:w-1/2 px-3">
                    <label for="nom" class="app-label"><i data-lucide="user"></i> Nom complet</label>
                    <input type="text" id="nom" name="nom" class="border rounded-md px-3 py-2 text-sm w-full" value="<?= e($utilisateur['nom']) ?>"
                        required>
                </div>
                <div class="md:w-1/2 px-3">
                    <label for="email" class="app-label"><i data-lucide="mail"></i> Adresse email</label>
                    <input type="email" id="email" class="border rounded-md px-3 py-2 text-sm w-full" value="<?= e($utilisateur['email']) ?>"
                        disabled>
                    <small class="text-gray-500">Email non modifiable</small>
                </div>
                <div class="md:w-1/3 px-3">
                    <label for="role" class="app-label"><i data-lucide="shield"></i> Rôle</label>
                    <select id="role" name="role" class="border rounded-md px-3 py-2 text-sm w-full bg-white" required>
                        <?php foreach ($userRoles as $role): ?>
                        <option value="<?= e($role) ?>" <?= $role === $utilisateur['role'] ? 'selected' : '' ?>>
                            <?= e($role) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:w-1/3 px-3">
                    <label for="actif" class="app-label"><i data-lucide="toggle-right"></i> Statut</label>
                    <select id="actif" name="actif" class="border rounded-md px-3 py-2 text-sm w-full bg-white">
                        <option value="1" <?= $utilisateur['actif'] ? 'selected' : '' ?>>Actif</option>
                        <option value="0" <?= !$utilisateur['actif'] ? 'selected' : '' ?>>Inactif</option>
                    </select>
                </div>
                <div class="md:w-1/3 px-3">
                    <label for="mot_de_passe" class="app-label"><i data-lucide="key"></i> Nouveau mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" class="border rounded-md px-3 py-2 text-sm w-full"
                        placeholder="Laissez vide pour garder l'actuel">
                </div>
                <div class="w-full px-3">
                    <hr>
                </div>
                <div class="w-full px-3 text-right">
                    <a href="<?= url('utilisateurs') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
                        <i data-lucide="x-circle"></i> Annuler
                    </a>
                    <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
                        <i data-lucide="check-circle"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php elseif (isset($utilisateurs)): ?>

<!-- Liste des utilisateurs -->
<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="users"></i> Gestion des utilisateurs</h1>
        <p class="text-gray-500">Liste des comptes et des rôles.</p>
    </div>
    <a href="<?= url('utilisateurs/create') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
        <i data-lucide="user-plus"></i> Ajouter un utilisateur
    </a>
</div>

    <div class="app-card shadow-sm mb-4">
    <div class="app-card-body">
        <?php if (empty($utilisateurs)): ?>
        <div class="text-center py-5">
            <i data-lucide="inbox" class="mx-auto h-12 w-12 text-slate-300"></i>
            <p class="mt-3 text-gray-500">Aucun utilisateur trouvé.</p>
            <a href="<?= url('utilisateurs/create') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white">
                <i data-lucide="user-plus"></i> Créer le premier utilisateur
            </a>
        </div>
        <?php else: ?>
            <div class="overflow-x-auto">
            <table class="app-table table-mobile-details">
            <thead>
                <tr>
                    <th class="hidden md:table-cell">#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th class="hidden md:table-cell">Créé le</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td class="hidden md:table-cell"><small class="text-gray-500">#<?= e($user['id_user']) ?></small></td>
                    <td><strong><span class="truncate" title="<?= e($user['nom']) ?>" data-toggle="tooltip"><?= e($user['nom']) ?></span></strong></td>
                    <td><span class="truncate" title="<?= e($user['email']) ?>" data-toggle="tooltip"><?= e($user['email']) ?></span></td>
                    <td><?= roleBadge($user['role']) ?></td>
                    <td>
                        <span class="app-badge app-badge-<?= e($user['actif'] ? 'success' : 'secondary') ?>">
                            <i data-lucide="<?= e($user['actif'] ? 'check-circle' : 'x-circle') ?>"></i>
                            <?= e($user['actif'] ? 'Actif' : 'Inactif') ?>
                        </span>
                    </td>
                    <td class="hidden md:table-cell"><small class="text-gray-500"><span class="truncate" title="<?= e(formatDate($user['date_creation'])) ?>" data-toggle="tooltip"><?= e(formatDate($user['date_creation'])) ?></span></small></td>
                    <td>
                        <a href="<?= url('utilisateurs/' . $user['id_user'] . '/edit') ?>"
                            class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-[#0d47a1] text-[#0d47a1] bg-transparent hover:bg-[#eef4ff]">
                            <i data-lucide="edit-2"></i> Modifier
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>

<?php
    $canSeeFinance = Auth::hasRole(['COMPTABLE', 'DG']);
    $moneyTrend = $variationMontantJour ?? null;
    $txTrend = $variationTransactionsJour ?? null;
    $trendClass = fn($value) => $value === null ? 'neutral' : ($value >= 0 ? 'up' : 'down');
    $trendText = fn($value) => $value === null ? 'Nouveau point' : (($value >= 0 ? '+' : '') . $value . '%');
    $serviceInitials = function (string $name): string {
        $parts = preg_split('/\s+/', trim($name));
        $letters = '';
        foreach (array_slice($parts ?: [], 0, 2) as $part) {
            $letters .= substr($part, 0, 1);
        }
        return strtoupper($letters ?: 'BK');
    };
    $stockPercent = function (?array $solde): int {
        if (!$solde || empty($solde['valeur_seuil'])) return 100;
        return max(0, min(140, (int)round(((float)$solde['montant_actuel'] / (float)$solde['valeur_seuil']) * 100)));
    };
?>

<div class="business-dashboard">
    <section class="dashboard-head">
        <div>
            <p class="dashboard-eyebrow">Pilotage BK Business</p>
            <h1>Bonjour, <?= e(Auth::nom() ?: 'Directeur') ?></h1>
            <p>Transactions, float, caisse, alertes et commissions à suivre aujourd'hui.</p>
        </div>
        <div class="dashboard-actions">
            <span class="date-pill"><i data-lucide="calendar-days"></i> <?= e(date('d M Y')) ?></span>
        </div>
    </section>

    <section class="kpi-grid">
        <article class="business-kpi-card accent-transactions">
            <div class="kpi-icon"><i data-lucide="arrow-left-right"></i></div>
            <p>Transactions</p>
            <strong><?= e($nbTransactionsJour) ?></strong>
            <span class="money-trend <?= e($trendClass($txTrend)) ?>"><?= e($trendText($txTrend)) ?> vs hier</span>
        </article>
        <article class="business-kpi-card accent-volume">
            <div class="kpi-icon"><i data-lucide="banknote"></i></div>
            <p>Volume traité</p>
            <strong><?= e(formatMontant((float)$totalMontantJour)) ?></strong>
            <span class="money-trend <?= e($trendClass($moneyTrend)) ?>"><?= e($trendText($moneyTrend)) ?> aujourd'hui</span>
        </article>
        <article class="business-kpi-card accent-alert">
            <div class="kpi-icon"><i data-lucide="siren"></i></div>
            <p>Alertes actives</p>
            <strong><?= e($nbAlertesActives) ?></strong>
            <span class="risk-badge <?= $nbAlertesActives > 0 ? 'danger' : 'success' ?>"><?= e($nbAlertesActives > 0 ? 'Action requise' : 'Services stables') ?></span>
        </article>
        <?php if ($canSeeFinance): ?>
            <article class="business-kpi-card accent-commission">
                <div class="kpi-icon"><i data-lucide="percent"></i></div>
                <p>Commissions mois</p>
                <strong><?= e(formatMontant((float)$totalCommissionsMois)) ?></strong>
                <span><?= e(date('m/Y')) ?></span>
            </article>
            <article class="business-kpi-card accent-margin">
                <div class="kpi-icon"><i data-lucide="chart-no-axes-combined"></i></div>
                <p>Rentabilité</p>
                <strong><?= e($rentabiliteMois['taux'] ?? 0) ?>%</strong>
                <div class="objective-bar"><span style="width: <?= e(min(100, (int)round((($rentabiliteMois['taux'] ?? 0) / max(1, ($rentabiliteMois['objectif'] ?? 15))) * 100))) ?>%"></span></div>
                <span>Objectif: <?= e($rentabiliteMois['objectif'] ?? 15) ?>%</span>
            </article>
        <?php endif; ?>
    </section>

    <section class="dashboard-grid-main">
        <article class="app-card chart-card">
            <div class="app-card-header">
                <span><i data-lucide="activity"></i> Évolution des transactions</span>
                <span class="app-badge app-badge-secondary">30 jours</span>
            </div>
            <div class="app-card-body">
                <canvas id="transactionsChart" data-chart='<?= e(json_encode($chartTransactions)) ?>'></canvas>
            </div>
        </article>

        <?php if ($canSeeFinance): ?>
            <article class="app-card chart-card">
                <div class="app-card-header">
                    <span><i data-lucide="badge-percent"></i> Évolution des commissions</span>
                    <span class="app-badge app-badge-secondary">30 jours</span>
                </div>
                <div class="app-card-body">
                    <canvas id="commissionsDailyChart" data-chart='<?= e(json_encode($chartCommissionsDaily ?? ['labels' => [], 'data' => []])) ?>'></canvas>
                </div>
            </article>
        <?php else: ?>
            <article class="app-card service-focus-card">
                <div class="app-card-header">
                    <span><i data-lucide="radio-tower"></i> Services sollicités</span>
                    <span class="app-badge app-badge-secondary">Activité</span>
                </div>
                <div class="app-card-body">
                    <?php foreach (array_slice($topServicesUsage ?? [], 0, 5) as $service): ?>
                        <div class="service-focus-row">
                            <span><?= e($service['nom_service']) ?></span>
                            <strong><?= e($service['total_transactions']) ?> tx</strong>
                            <small><?= e(formatMontant((float)$service['total_montant'])) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>
        <?php endif; ?>

        <aside class="app-card alert-panel">
            <div class="app-card-header">
                <span><i data-lucide="bell-ring"></i> Alertes récentes</span>
                <a href="<?= url('alertes') ?>">Voir tout</a>
            </div>
            <div class="app-card-body">
                <?php if (empty($alertesActives)): ?>
                    <div class="empty-state compact"><i data-lucide="shield-check"></i><span>Aucune alerte active.</span></div>
                <?php else: ?>
                    <div class="alert-list">
                        <?php foreach (array_slice($alertesActives, 0, 4) as $alerte): ?>
                            <div class="alert-card <?= e($alerte['criticite']) ?>">
                                <div>
                                    <strong><?= e($alerte['nom_service']) ?> - <?= e(ucfirst(strtolower($alerte['type_solde']))) ?></strong>
                                    <span><?= e(formatMontant((float)$alerte['montant_actuel'])) ?> / seuil <?= e(formatMontant((float)$alerte['valeur_seuil'])) ?></span>
                                    <small>Écart <?= e(formatMontant((float)$alerte['ecart_seuil'])) ?></small>
                                </div>
                                <a href="<?= url('stocks') ?>" aria-label="Voir stock"><i data-lucide="arrow-up-right"></i></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </section>

    <section class="dashboard-grid-secondary">
        <article class="app-card balances-card">
            <div class="app-card-header">
                <span><i data-lucide="wallet-cards"></i> Soldes par service</span>
                <div class="table-actions">
                    <span class="app-badge app-badge-primary">Float</span>
                    <span class="app-badge app-badge-secondary">Caisse</span>
                    <a href="<?= url('stocks') ?>" class="app-btn app-btn-sm app-btn-secondary"><i data-lucide="eye"></i> Stocks</a>
                </div>
            </div>
            <div class="app-card-body p-0">
                <div class="overflow-x-auto">
                    <table class="app-table service-balance-table table-mobile-details">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th class="text-right">Float</th>
                                <th class="text-right">Seuil float</th>
                                <th class="text-right">Caisse</th>
                                <th class="text-right">Seuil caisse</th>
                                <th>État</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($soldesParService as $service): ?>
                                <?php
                                    $float = $service['float'];
                                    $caisse = $service['caisse'];
                                ?>
                                <tr>
                                    <td>
                                        <div class="service-cell">
                                            <span class="service-avatar"><?= e($serviceInitials($service['nom_service'])) ?></span>
                                            <div>
                                                <strong><?= e($service['nom_service']) ?></strong>
                                                <small><?= e($service['categorie']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <?= $float ? e(formatMontant((float)$float['montant_actuel'])) : '—' ?>
                                        <div class="stock-level-bar"><span style="width: <?= e($stockPercent($float)) ?>%"></span></div>
                                    </td>
                                    <td class="text-right"><?= $float && $float['valeur_seuil'] !== null ? e(formatMontant((float)$float['valeur_seuil'])) : 'N/A' ?></td>
                                    <td class="text-right">
                                        <?= $caisse ? e(formatMontant((float)$caisse['montant_actuel'])) : '—' ?>
                                        <div class="stock-level-bar caisse"><span style="width: <?= e($stockPercent($caisse)) ?>%"></span></div>
                                    </td>
                                    <td class="text-right"><?= $caisse && $caisse['valeur_seuil'] !== null ? e(formatMontant((float)$caisse['valeur_seuil'])) : 'N/A' ?></td>
                                    <td><span class="risk-badge <?= $service['en_alerte'] ? 'danger' : 'success' ?>"><?= e($service['en_alerte'] ? 'Alerte' : 'OK') ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </article>

        <?php if ($canSeeFinance): ?>
            <article class="app-card commission-card">
                <div class="app-card-header">
                    <span><i data-lucide="pie-chart"></i> Répartition des commissions</span>
                    <span class="app-badge app-badge-secondary">Mois</span>
                </div>
                <div class="app-card-body commission-layout">
                    <div class="donut-wrap">
                        <canvas id="commissionsChart" data-chart='<?= e(json_encode($chartCommissions)) ?>'></canvas>
                    </div>
                    <div class="commission-list">
                        <?php foreach (array_slice($beneficesParService ?? [], 0, 5) as $index => $benefice): ?>
                            <?php $share = $totalCommissionsMois > 0 ? round(((float)$benefice['total_commission'] / (float)$totalCommissionsMois) * 100) : 0; ?>
                            <div>
                                <span class="legend-dot legend-<?= e($index + 1) ?>"></span>
                                <strong><?= e($benefice['nom_service']) ?></strong>
                                <b><?= e($share) ?>%</b>
                                <small><?= e(formatMontant((float)$benefice['total_commission'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </article>
        <?php endif; ?>
    </section>

    <section class="dashboard-grid-bottom">
        <?php if ($canSeeFinance): ?>
            <article class="app-card profit-card">
                <div class="app-card-header"><span><i data-lucide="landmark"></i> Bénéfices par service</span></div>
                <div class="app-card-body">
                    <?php foreach (array_slice($topProfitServices ?? [], 0, 4) as $profit): ?>
                        <?php $maxProfit = max(1, (float)($topProfitServices[0]['total_commission'] ?? 1)); ?>
                        <div class="profit-row">
                            <span><?= e($profit['nom_service']) ?></span>
                            <strong><?= e(formatMontant((float)$profit['total_commission'])) ?></strong>
                            <div><span style="width: <?= e((int)round(((float)$profit['total_commission'] / $maxProfit) * 100)) ?>%"></span></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>
        <?php endif; ?>

        <article class="app-card transactions-card">
            <div class="app-card-header">
                <span><i data-lucide="receipt-text"></i> Dernières transactions</span>
                <a href="<?= url('transactions') ?>">Voir tout</a>
            </div>
            <div class="app-card-body p-0">
                <div class="overflow-x-auto">
                    <table class="app-table table-mobile-details">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Service</th>
                                <th>Type</th>
                                <th class="text-right">Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dernièresTransactions as $tx): ?>
                                <tr>
                                    <td class="text-xs text-slate-500">#<?= e($tx['id_transaction']) ?></td>
                                    <td class="font-semibold text-slate-800"><?= e($tx['nom_service']) ?></td>
                                    <td><?= e($tx['libelle_type']) ?></td>
                                    <td class="text-right font-semibold"><?= e(formatMontant((float)$tx['montant'])) ?></td>
                                    <td><span class="risk-badge success"><?= e(ucfirst(strtolower($tx['statut'] ?? 'validée'))) ?></span></td>
                                    <td class="text-xs text-slate-500"><?= e(formatDate($tx['date_heure'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </article>
    </section>
</div>

<?php endif; ?>
