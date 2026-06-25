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
        Session::regenerate();
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
            if (self::wantsJson()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Non authentifié']);
                exit;
            }
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    // Rediriger si rôle insuffisant
    public static function requireRole(array $roles): void {
        self::requireAuth();
        if (!self::hasRole($roles)) {
            if (self::wantsJson()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Accès refusé']);
                exit;
            }
            header('Location: ' . BASE_URL . '/dashboard?error=access_denied');
            exit;
        }
    }

    // Détecte si la requête attend une réponse JSON (API / AJAX)
    private static function wantsJson(): bool {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($uri, PHP_URL_PATH) ?: '';
        $isApiPath = stripos($path, '/api/') !== false;
        return $xhr || stripos($accept, 'application/json') !== false || $isApiPath;
    }

    // Fournit les informations minimales de l'utilisateur connecté
    public static function user(): array {
        return [
            'id' => self::id(),
            'nom' => self::nom(),
            'role' => self::role()
        ];
    }
}
