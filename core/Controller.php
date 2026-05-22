<?php
// ============================================================
//  core/Controller.php — Classe de base pour les contrôleurs
// ============================================================
abstract class Controller {

    // Charger une vue avec des données
    protected function view(string $view, array $data = []): void {
        extract($data); // rend les variables disponibles dans la vue
        $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            die("Vue introuvable : {$viewPath}");
        }
        require_once $viewPath;
    }

    // Charger une vue avec le layout complet
    protected function render(string $view, array $data = [], string $title = ''): void {
        $data['pageTitle'] = $title ?: APP_NAME;
        $data['view']      = $view;
        extract($data);
        require_once VIEWS_PATH . '/layouts/header.php';
        require_once VIEWS_PATH . '/layouts/navbar.php';
        require_once VIEWS_PATH . '/layouts/sidebar.php';
        $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        require_once $viewPath;
        require_once VIEWS_PATH . '/layouts/footer.php';
    }

    // Redirection
    protected function redirect(string $path): void {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    // Réponse JSON (pour les appels AJAX)
    protected function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Récupérer une valeur POST nettoyée
    protected function post(string $key, mixed $default = null): mixed {
        if (!isset($_POST[$key])) {
            return $default;
        }

        $value = $_POST[$key];
        return is_string($value) ? trim($value) : $value;
    }

    // Récupérer une valeur GET nettoyée
    protected function get(string $key, mixed $default = null): mixed {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    // Vérifier le token CSRF
    protected function verifyCsrf(): void {
        $token = $this->post('csrf_token');
        if (!$token || $token !== Session::get('csrf_token')) {
            Session::flash('error', 'Jeton CSRF invalide. Veuillez réessayer.');
            $this->redirect('dashboard');
        }
    }

    protected function validate(array $data, array $rules): array {
        return Validator::validate($data, $rules);
    }

    protected function abortValidation(array $errors, string $redirectPath): void {
        if (!empty($errors)) {
            Session::flash('error', implode(' ', $errors));
            $this->redirect($redirectPath);
        }
    }
}
