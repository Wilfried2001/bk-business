<?php
// ============================================================
//  core/Helpers.php — Fonctions utilitaires globales
// ============================================================

// Formater un montant en FCFA
function formatMontant(float $montant): string {
    return number_format($montant, 0, ',', ' ') . ' FCFA';
}

// Formater une date
function formatDate(string $date, string $format = 'd/m/Y H:i'): string {
    return date($format, strtotime($date));
}

// Échapper les sorties HTML (sécurité XSS)
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Générer un token CSRF
function generateCsrf(): string {
    $existing = Session::get('csrf_token');
    if (!empty($existing)) {
        return $existing;
    }

    $token = bin2hex(random_bytes(32));
    Session::set('csrf_token', $token);
    return $token;
}

// Champ CSRF pour les formulaires
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . generateCsrf() . '">';
}

// Badge couleur selon le rôle
function roleBadge(string $role): string {
    $colors = [
        'AGENT'       => 'primary',
        'SUPERVISEUR' => 'warning',
        'COMPTABLE'   => 'info',
        'DG'          => 'danger',
    ];
    $color = $colors[$role] ?? 'secondary';
    return "<span class='badge bg-" . e($color) . "'>" . e($role) . "</span>";
}

// Badge couleur selon le statut d'alerte
function alerteBadge(string $statut): string {
    $color = $statut === 'ACTIVE' ? 'danger' : 'success';
    return "<span class='badge bg-" . e($color) . "'>" . e($statut) . "</span>";
}

// Badge couleur selon la nature du mouvement
function mouvementBadge(string $nature): string {
    $color = $nature === 'CREDIT' ? 'success' : 'danger';
    $icon  = $nature === 'CREDIT' ? '↑' : '↓';
    return "<span class='badge bg-" . e($color) . "'>" . e($icon . ' ' . $nature) . "</span>";
}

// Vérifier si un champ est vide
function isEmpty(mixed $value): bool {
    return $value === null || trim((string)$value) === '';
}

// URL helper
function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}
