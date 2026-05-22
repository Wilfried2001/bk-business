<?php
// ============================================================
//  core/Auth.php — Authentification et contrôle des rôles
// ============================================================
class Auth {

    // Connecter un utilisateur
    public static function login(array $user): void {
        Session::set('user_id',   $user['id_user']);
        Session::set('user_nom',  $user['nom']);
        Session::set('user_role', $user['role']);
        Session::set('logged_in', true);
    }

    // Déconnecter
    public static function logout(): void {
        Session::destroy();
    }

    // Vérifier si connecté
    public static function check(): bool {
        return Session::get('logged_in', false) === true;
    }

    // Récupérer le rôle
    public static function role(): string {
        return Session::get('user_role', '');
    }

    // Récupérer l'id
    public static function id(): int {
        return (int) Session::get('user_id', 0);
    }

    // Récupérer le nom
    public static function nom(): string {
        return Session::get('user_nom', '');
    }

    // Vérifier si l'utilisateur a l'un des rôles autorisés
    public static function hasRole(array $roles): bool {
        return in_array(self::role(), $roles);
    }

    // Rediriger si non connecté
    public static function requireAuth(): void {
        if (!self::check()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    // Rediriger si rôle insuffisant
    public static function requireRole(array $roles): void {
        self::requireAuth();
        if (!self::hasRole($roles)) {
            header('Location: ' . BASE_URL . '/dashboard?error=access_denied');
            exit;
        }
    }
}
