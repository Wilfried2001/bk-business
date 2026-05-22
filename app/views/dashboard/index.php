<?php if (isset($userRoles) && !isset($utilisateurs) && !isset($utilisateur)): ?>

<!-- Créer un utilisateur -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-person-plus"></i> Créer un utilisateur</h1>
        <p class="text-muted">Ajouter un nouveau compte utilisateur à l'application.</p>
    </div>
    <a href="<?= url('utilisateurs') ?>" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <strong>Informations utilisateur</strong>
    </div>
    <div class="card-body">
        <form action="<?= url('utilisateurs/store') ?>" method="post">
            <?= csrfField() ?>
            <div class="row gy-3">
                <div class="col-md-6">
                    <label for="nom" class="form-label"><i class="bi bi-person"></i> Nom complet</label>
                    <input type="text" id="nom" name="nom" class="form-control" placeholder="Ex: Jean Dupont" required>
                <div class="col-md-6">
                    <label for="email" class="form-label"><i class="bi bi-envelope"></i> Adresse email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Ex: jean@example.com"
                        required>
                </div>
                <div class="col-md-4">
                    <label for="role" class="form-label"><i class="bi bi-shield"></i> Rôle</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="">Sélectionner un rôle</option>
                        <?php foreach ($userRoles as $role): ?>
                        <option value="<?= e($role) ?>"><?= e($role) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="mot_de_passe" class="form-label"><i class="bi bi-key"></i> Mot de passe
                        temporaire</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control"
                        placeholder="Mot de passe initial" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Créer l'utilisateur
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php elseif (isset($utilisateur)): ?>

<!-- Modifier un utilisateur -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-pencil-square"></i> Modifier l'utilisateur</h1>
        <p class="text-muted">Mettre à jour les informations du compte.</p>
    </div>
    <a href="<?= url('utilisateurs') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <strong>Détails du compte</strong>
    </div>
    <div class="card-body">
        <form action="<?= url('utilisateurs/' . (int)$utilisateur['id_user'] . '/edit') ?>" method="post">
            <?= csrfField() ?>
            <div class="row gy-3">
                <div class="col-md-6">
                    <label for="nom" class="form-label"><i class="bi bi-person"></i> Nom complet</label>
                    <input type="text" id="nom" name="nom" class="form-control" value="<?= e($utilisateur['nom']) ?>"
                        required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label"><i class="bi bi-envelope"></i> Adresse email</label>
                    <input type="email" id="email" class="form-control" value="<?= e($utilisateur['email']) ?>"
                        disabled>
                    <small class="text-muted">Email non modifiable</small>
                </div>
                <div class="col-md-4">
                    <label for="role" class="form-label"><i class="bi bi-shield"></i> Rôle</label>
                    <select id="role" name="role" class="form-select" required>
                        <?php foreach ($userRoles as $role): ?>
                        <option value="<?= e($role) ?>" <?= $role === $utilisateur['role'] ? 'selected' : '' ?>>
                            <?= e($role) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="actif" class="form-label"><i class="bi bi-toggle-on"></i> Statut</label>
                    <select id="actif" name="actif" class="form-select">
                        <option value="1" <?= $utilisateur['actif'] ? 'selected' : '' ?>>Actif</option>
                        <option value="0" <?= !$utilisateur['actif'] ? 'selected' : '' ?>>Inactif</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="mot_de_passe" class="form-label"><i class="bi bi-key"></i> Nouveau mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control"
                        placeholder="Laissez vide pour garder l'actuel">
                </div>
                <div class="col-12">
                    <hr>
                </div>
                <div class="col-12 text-end">
                    <a href="<?= url('utilisateurs') ?>" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php elseif (isset($utilisateurs)): ?>

<!-- Liste des utilisateurs -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-people"></i> Gestion des utilisateurs</h1>
        <p class="text-muted">Liste des comptes et des rôles.</p>
    </div>
    <a href="<?= url('utilisateurs/create') ?>" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Ajouter un utilisateur
    </a>
</div>

    <div class="card shadow-sm mb-4">
    <div class="card-body">
        <?php if (empty($utilisateurs)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
            <p class="mt-3 text-muted">Aucun utilisateur trouvé.</p>
            <a href="<?= url('utilisateurs/create') ?>" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Créer le premier utilisateur
            </a>
        </div>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-mobile-details">
            <thead>
                <tr>
                    <th class="d-none d-md-table-cell">#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th class="d-none d-md-table-cell">Créé le</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td class="d-none d-md-table-cell"><small class="text-muted">#<?= e($user['id_user']) ?></small></td>
                    <td><strong><span class="truncate" title="<?= e($user['nom']) ?>" data-bs-toggle="tooltip"><?= e($user['nom']) ?></span></strong></td>
                    <td><span class="truncate" title="<?= e($user['email']) ?>" data-bs-toggle="tooltip"><?= e($user['email']) ?></span></td>
                    <td><?= roleBadge($user['role']) ?></td>
                    <td>
                        <span class="badge bg-<?= $user['actif'] ? 'success' : 'secondary' ?>">
                            <i class="bi bi-<?= $user['actif'] ? 'check-circle' : 'x-circle' ?>"></i>
                            <?= $user['actif'] ? 'Actif' : 'Inactif' ?>
                        </span>
                    </td>
                    <td class="d-none d-md-table-cell"><small class="text-muted"><span class="truncate" title="<?= e(formatDate($user['date_creation'])) ?>" data-bs-toggle="tooltip"><?= e(formatDate($user['date_creation'])) ?></span></small></td>
                    <td>
                        <a href="<?= url('utilisateurs/' . $user['id_user'] . '/edit') ?>"
                            class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Modifier
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
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-speedometer2"></i> Tableau de bord</h1>
        <p class="text-muted">Vue synthétique des transactions, des stocks et des alertes.</p>
    </div>
</div>

<!-- Statistiques principales -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <h6>Transactions du jour</h6>
            <h3><?= e($nbTransactionsJour) ?></h3>
            <small class="text-muted d-block mt-2">
                <i class="bi bi-arrow-up"></i> Opérations validées
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="bi bi-cash-coin"></i>
            </div>
            <h6>Montant validé</h6>
            <h3><?= e(formatMontant($totalMontantJour)) ?></h3>
            <small class="text-muted d-block mt-2">
                <i class="bi bi-arrow-up"></i> Aujourd'hui
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h6>Alertes actives</h6>
            <h3><?= e($nbAlertesActives) ?></h3>
            <small class="text-muted d-block mt-2">
                <i class="bi bi-info-circle"></i> À traiter
            </small>
        </div>
    </div>
    <?php if (isset($totalCommissionsMois)): ?>
    <div class="col-md-3">
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="bi bi-percent"></i>
            </div>
            <h6>Commissions ce mois</h6>
            <h3><?= e(formatMontant($totalCommissionsMois)) ?></h3>
            <small class="text-muted d-block mt-2">
                <i class="bi bi-calendar3"></i> Calculées
            </small>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Contenu principal -->
<div class="row g-4">
    <!-- Graphiques -->
    <div class="col-12">
        <div class="row gy-3">
            <div class="col-lg-6">
                <div class="card shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div><i class="bi bi-graph-up"></i> Transactions (7 derniers jours)</div>
                        <button class="btn btn-sm btn-outline-secondary d-lg-none" data-bs-toggle="collapse" data-bs-target="#chartTransactionsWrap">Voir</button>
                    </div>
                    <div class="card-body collapse show" id="chartTransactionsWrap">
                        <canvas id="transactionsChart" height="120" data-chart='<?= e(json_encode($chartTransactions)) ?>'></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div><i class="bi bi-pie-chart"></i> Commissions (par service)</div>
                        <button class="btn btn-sm btn-outline-secondary d-lg-none" data-bs-toggle="collapse" data-bs-target="#chartCommissionsWrap">Voir</button>
                    </div>
                    <div class="card-body collapse show" id="chartCommissionsWrap">
                        <canvas id="commissionsChart" height="120" data-chart='<?= e(json_encode($chartCommissions)) ?>'></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Soldes des services -->
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-boxes"></i> Soldes de services
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-mobile-details">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Seuil</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($soldes as $solde): ?>
                            <tr>
                                <td><strong><?= e($solde['nom_service']) ?></strong></td>
                                <td><small class="text-muted"><?= e($solde['type_solde']) ?></small></td>
                                <td><?= e(formatMontant((float)$solde['montant_actuel'])) ?></td>
                                <td class="text-muted">
                                    <?= $solde['valeur_seuil'] !== null ? e(formatMontant((float)$solde['valeur_seuil'])) : '—' ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $solde['en_alerte'] ? 'danger' : 'success' ?>">
                                        <i
                                            class="bi bi-<?= $solde['en_alerte'] ? 'exclamation-circle' : 'check-circle' ?>"></i>
                                        <?= $solde['en_alerte'] ? 'Alerte' : 'Normal' ?>
                                    </span>
                                </td>
                                    <td class="text-end">
                                        <a href="<?= url('stocks/' . $solde['id_service']) ?>" class="btn btn-sm btn-outline-secondary">Détails</a>
                                    </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières transactions -->
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Dernières transactions
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-mobile-details">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Agent</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dernièresTransactions as $tx): ?>
                            <tr>
                                <td><small><?= e($tx['id_transaction']) ?></small></td>
                                <td><small><?= e(formatDate($tx['date_heure'])) ?></small></td>
                                <td><strong><?= e($tx['nom_service']) ?></strong></td>
                                <td><small class="text-muted"><?= e($tx['libelle_type']) ?></small></td>
                                <td><span class="text-success">+<?= e(formatMontant((float)$tx['montant'])) ?></span>
                                </td>
                                <td><small><?= e($tx['nom_agent']) ?></small></td>
                                <td></td>
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