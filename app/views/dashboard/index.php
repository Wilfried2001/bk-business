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

<!-- Dashboard Principal -->
<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="flex items-center gap-2 text-2xl font-bold text-slate-900"><i data-lucide="gauge"></i> Tableau de bord</h1>
        <p class="text-sm text-slate-500">Vue synthétique des transactions, des stocks et des alertes.</p>
    </div>
</div>

<!-- Statistiques principales -->
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="app-stat-card primary h-full">
        <div class="mb-4 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Transactions du jour</p>
                <p class="mt-2 text-3xl font-bold text-primary"><?= e($nbTransactionsJour) ?></p>
            </div>
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-md bg-primary/10 text-primary"><i data-lucide="arrow-left-right"></i></span>
        </div>
        <p class="flex items-center gap-1 text-xs text-slate-500"><i data-lucide="arrow-up"></i> Opérations validées</p>
    </div>

    <div class="app-stat-card success h-full">
        <div class="mb-4 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Montant validé</p>
                <p class="mt-2 text-2xl font-bold text-success"><?= e(formatMontant($totalMontantJour)) ?></p>
            </div>
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-md bg-success/10 text-success"><i data-lucide="dollar-sign"></i></span>
        </div>
        <p class="flex items-center gap-1 text-xs text-slate-500"><i data-lucide="arrow-up"></i> Aujourd'hui</p>
    </div>

    <div class="app-stat-card warning h-full">
        <div class="mb-4 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Alertes actives</p>
                <p class="mt-2 text-3xl font-bold text-warning"><?= e($nbAlertesActives) ?></p>
            </div>
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-md bg-warning/10 text-warning"><i data-lucide="alert-triangle"></i></span>
        </div>
        <p class="flex items-center gap-1 text-xs text-slate-500"><i data-lucide="info"></i> À traiter</p>
    </div>

    <?php if (isset($totalCommissionsMois)): ?>
    <div class="app-stat-card h-full border-l-info">
        <div class="mb-4 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Commissions ce mois</p>
                <p class="mt-2 text-2xl font-bold text-info"><?= e(formatMontant($totalCommissionsMois)) ?></p>
            </div>
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-md bg-info/10 text-info"><i data-lucide="percent"></i></span>
        </div>
        <p class="flex items-center gap-1 text-xs text-slate-500"><i data-lucide="calendar"></i> Calculées</p>
    </div>
    <?php endif; ?>
</div>

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="app-card h-full">
            <div class="app-card-header"><span class="flex items-center gap-2"><i data-lucide="trending-up"></i> Transactions (7 derniers jours)</span></div>
            <div class="app-card-body h-72">
                <canvas id="transactionsChart" data-chart='<?= e(json_encode($chartTransactions)) ?>'></canvas>
            </div>
        </div>

        <div class="app-card h-full">
            <div class="app-card-header"><span class="flex items-center gap-2"><i data-lucide="pie-chart"></i> Commissions (par service)</span></div>
            <div class="app-card-body h-72">
                <canvas id="commissionsChart" data-chart='<?= e(json_encode($chartCommissions)) ?>'></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 2xl:grid-cols-4">
        <div class="app-card h-full">
            <div class="app-card-header">
                <span class="flex items-center gap-2"><i data-lucide="bar-chart"></i> Services les plus utilisés</span>
            </div>
            <div class="app-card-body">
                <?php if (empty($topServicesUsage)): ?>
                    <p class="text-sm text-slate-500">Aucune donnée d'utilisation disponible.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="app-table app-table-compact min-w-[22rem]">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th class="text-right">Transactions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topServicesUsage as $service): ?>
                                <tr>
                                    <td class="font-medium text-slate-800"><?= e($service['nom_service']) ?></td>
                                    <td class="text-right font-semibold"><?= e($service['total_transactions']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="app-card h-full">
            <div class="app-card-header">
                <span class="flex items-center gap-2"><i data-lucide="dollar-sign"></i> Services les plus valorisés</span>
            </div>
            <div class="app-card-body">
                <?php if (empty($topServicesMontant)): ?>
                    <p class="text-sm text-slate-500">Aucune donnée de montant disponible.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="app-table app-table-compact min-w-[24rem]">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th class="text-right">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topServicesMontant as $service): ?>
                                <tr>
                                    <td class="font-medium text-slate-800"><?= e($service['nom_service']) ?></td>
                                    <td class="text-right font-semibold"><?= e(formatMontant((float)$service['total_montant'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="app-card h-full">
            <div class="app-card-header">
                <span class="flex items-center gap-2"><i data-lucide="bell"></i> Services avec alertes</span>
            </div>
            <div class="app-card-body">
                <?php if (empty($topAlertServices)): ?>
                    <p class="text-sm text-slate-500">Aucun service en alerte pour le moment.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="app-table app-table-compact min-w-[22rem]">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th class="text-right">Alertes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topAlertServices as $service): ?>
                                <tr>
                                    <td class="font-medium text-slate-800"><?= e($service['nom_service']) ?></td>
                                    <td class="text-right font-semibold text-danger"><?= e($service['active_alerts']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="app-card h-full">
            <div class="app-card-header">
                <span class="flex items-center gap-2"><i data-lucide="trending-up"></i> Services rentables</span>
            </div>
            <div class="app-card-body">
                <?php if (empty($topProfitServices)): ?>
                    <p class="text-sm text-slate-500">Aucune donnée de rentabilité disponible.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="app-table app-table-compact min-w-[24rem]">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th class="text-right">Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topProfitServices as $service): ?>
                                <tr>
                                    <td class="font-medium text-slate-800"><?= e($service['nom_service']) ?></td>
                                    <td class="text-right font-semibold"><?= e(formatMontant((float)$service['total_commission'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="app-card">
            <div class="app-card-header">
                <span class="flex items-center gap-2"><i data-lucide="package"></i> Soldes de services</span>
            </div>
            <div class="app-card-body p-0">
                <div class="overflow-x-auto">
                    <table class="app-table table-mobile-details min-w-[44rem]">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Type</th>
                                <th class="text-right">Montant</th>
                                <th class="text-right">Seuil</th>
                                <th>Statut</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($soldes as $solde): ?>
                            <tr>
                                <td class="font-semibold text-slate-800"><?= e($solde['nom_service']) ?></td>
                                <td><span class="app-badge app-badge-secondary"><?= e($solde['type_solde']) ?></span></td>
                                <td class="text-right font-medium"><?= e(formatMontant((float)$solde['montant_actuel'])) ?></td>
                                <td class="text-right text-slate-500">
                                    <?= $solde['valeur_seuil'] !== null ? e(formatMontant((float)$solde['valeur_seuil'])) : '—' ?>
                                </td>
                                <td>
                                    <span class="app-badge app-badge-<?= e($solde['en_alerte'] ? 'danger' : 'success') ?>">
                                        <i data-lucide="<?= e($solde['en_alerte'] ? 'alert-circle' : 'check-circle') ?>"></i>
                                        <?= e($solde['en_alerte'] ? 'Alerte' : 'Normal') ?>
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="<?= url('stocks/' . $solde['id_service']) ?>" class="app-btn app-btn-sm app-btn-secondary">
                                        <i data-lucide="eye"></i> Détails
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="app-card-header">
                <span class="flex items-center gap-2"><i data-lucide="history"></i> Dernières transactions</span>
            </div>
            <div class="app-card-body p-0">
                <div class="overflow-x-auto">
                    <table class="app-table table-mobile-details min-w-[48rem]">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Type</th>
                                <th class="text-right">Montant</th>
                                <th>Agent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dernièresTransactions as $tx): ?>
                            <tr>
                                <td class="text-xs text-slate-500">#<?= e($tx['id_transaction']) ?></td>
                                <td class="text-xs text-slate-500"><?= e(formatDate($tx['date_heure'])) ?></td>
                                <td class="font-semibold text-slate-800"><?= e($tx['nom_service']) ?></td>
                                <td><span class="app-badge app-badge-secondary"><?= e($tx['libelle_type']) ?></span></td>
                                <td class="text-right font-semibold text-success"><?= e(formatMontant((float)$tx['montant'])) ?></td>
                                <td><?= e($tx['nom_agent']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
