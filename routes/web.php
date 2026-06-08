<?php
// ============================================================
//  routes/web.php — Toutes les routes de l'application
// ============================================================

// ── Authentification ─────────────────────────────────────────
$router->get( '/auth/login',  'Auth', 'loginForm');
$router->post('/auth/login',  'Auth', 'login');
$router->get( '/auth/logout', 'Auth', 'logout', ['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG']);

// ── Dashboard ────────────────────────────────────────────────
$router->get('/dashboard',    'Dashboard', 'index', ['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG']);
$router->get('/',             'Dashboard', 'index', ['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG']);

// ── Transactions ─────────────────────────────────────────────
$router->get( '/transactions',           'Transaction', 'index', ['AGENT', 'SUPERVISEUR', 'DG']);
$router->get( '/transactions/create',    'Transaction', 'create', ['AGENT', 'SUPERVISEUR', 'DG']);
$router->post('/transactions/store',     'Transaction', 'store', ['AGENT', 'SUPERVISEUR', 'DG']);
$router->get( '/transactions/:id',         'Transaction', 'show', ['AGENT', 'SUPERVISEUR', 'DG']);
$router->get( '/transactions/:id/edit',    'Transaction', 'edit', ['SUPERVISEUR', 'DG']);
$router->post('/transactions/:id/update',  'Transaction', 'update', ['SUPERVISEUR', 'DG']);
$router->post('/transactions/:id/cancel',  'Transaction', 'cancel', ['AGENT', 'SUPERVISEUR', 'DG']);

// ── Stocks ───────────────────────────────────────────────────
$router->get('/stocks',           'Stock', 'index', ['SUPERVISEUR', 'COMPTABLE', 'DG']);
$router->get('/stocks/:id',       'Stock', 'show', ['SUPERVISEUR', 'COMPTABLE', 'DG']);
$router->post('/stocks/:id/seuil','Stock', 'saveThreshold', ['SUPERVISEUR', 'COMPTABLE', 'DG']);
// Mettre à jour le montant actuel d'un solde (initial ou ajustement)
$router->post('/stocks/:id/solde','Stock', 'updateSolde', ['SUPERVISEUR', 'COMPTABLE', 'DG']);

// Définir les stocks initiaux pour tous les services (formulaire global)
$router->get('/stocks/define',  'Stock', 'defineForm', ['SUPERVISEUR', 'COMPTABLE', 'DG']);
$router->post('/stocks/define', 'Stock', 'defineStore', ['SUPERVISEUR', 'COMPTABLE', 'DG']);

// Gérer les seuils pour tous les services (formulaire global)
$router->get('/stocks/seuils/all',  'Stock', 'seuilsForm', ['SUPERVISEUR', 'COMPTABLE', 'DG']);
$router->post('/stocks/seuils/save', 'Stock', 'seuilsSave', ['SUPERVISEUR', 'COMPTABLE', 'DG']);

// ── Alertes ──────────────────────────────────────────────────
$router->get( '/alertes',              'Alerte', 'index', ['SUPERVISEUR', 'DG']);
$router->post('/alertes/:id/traiter',  'Alerte', 'traiter', ['SUPERVISEUR', 'DG']);

// ── Commissions (Comptable + DG) ─────────────────────────────
$router->get( '/commissions',          'Commission', 'index', ['COMPTABLE', 'DG']);
$router->get( '/commissions/config',   'Commission', 'config', ['COMPTABLE', 'DG']);
$router->post('/commissions/config',   'Commission', 'saveConfig', ['COMPTABLE', 'DG']);

// ── Rapports ─────────────────────────────────────────────────
$router->get('/rapports',              'Rapport', 'index', ['SUPERVISEUR', 'COMPTABLE', 'DG']);
$router->get('/rapports/export',       'Rapport', 'export', ['SUPERVISEUR', 'COMPTABLE', 'DG']);

// ── Utilisateurs (DG uniquement) ─────────────────────────────
$router->get( '/utilisateurs',          'Utilisateur', 'index', ['DG']);
$router->get( '/utilisateurs/create',   'Utilisateur', 'create', ['DG']);
$router->post('/utilisateurs/store',    'Utilisateur', 'store', ['DG']);
$router->get( '/utilisateurs/:id/edit', 'Utilisateur', 'edit', ['DG']);
$router->post('/utilisateurs/:id/edit', 'Utilisateur', 'update', ['DG']);
