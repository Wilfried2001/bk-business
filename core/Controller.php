<?php
// ============================================================
//  core/Controller.php — Classe de base pour les contrôleurs
// ============================================================
abstract class Controller {

    // Charger une vue avec des données
    protected function view(string $view, array $data = []): void {
        extract($data, EXTR_SKIP); // évite d’écraser les variables locales existantes
        $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new RuntimeException("Vue introuvable : {$viewPath}");
        }
        require_once $viewPath;
    }

    // Charger une vue avec le layout complet
    protected function render(string $view, array $data = [], string $title = ''): void {
        $data['pageTitle'] = $title ?: APP_NAME;
        $data['view']      = $view;
        $data['sidebarHealth'] = $data['sidebarHealth'] ?? $this->getSidebarHealth();
        extract($data, EXTR_SKIP);
        require_once VIEWS_PATH . '/layouts/header.php';
        require_once VIEWS_PATH . '/layouts/navbar.php';
        require_once VIEWS_PATH . '/layouts/sidebar.php';
        $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new RuntimeException("Vue introuvable : {$viewPath}");
        }
        require_once $viewPath;
        require_once VIEWS_PATH . '/layouts/footer.php';
    }

    private function getSidebarHealth(): array {
        try {
            $alerteModel = new AlerteSolde();
            $soldeModel = new SoldeService();
            $txModel = new Transaction();

            $nbAlertes = $alerteModel->compterActives();
            $disponibiliteSoldes = $soldeModel->getDisponibilitePourcentage();
            $nbTransactionsJour = $txModel->getNbJour();

            $penaliteAlertes = min(40, $nbAlertes * 8);
            $penaliteSoldes = (int)round((100 - $disponibiliteSoldes) * 0.35);
            $penaliteActivite = $nbTransactionsJour > 0 ? 0 : 15;
            $score = max(0, min(100, 100 - $penaliteAlertes - $penaliteSoldes - $penaliteActivite));

            if ($score >= 85) {
                $statut = 'Stable';
                $couleur = '#10b981';
            } elseif ($score >= 65) {
                $statut = 'Attention';
                $couleur = '#f59e0b';
            } else {
                $statut = 'Critique';
                $couleur = '#d32f2f';
            }

            return [
                'score' => $score,
                'statut' => $statut,
                'couleur' => $couleur,
                'nb_alertes' => $nbAlertes,
                'disponibilite_soldes' => $disponibiliteSoldes,
                'nb_transactions_jour' => $nbTransactionsJour,
            ];
        } catch (Throwable $e) {
            return [
                'score' => 0,
                'statut' => 'Indisponible',
                'couleur' => '#64748b',
                'nb_alertes' => 0,
                'disponibilite_soldes' => 0,
                'nb_transactions_jour' => 0,
            ];
        }
    }

    // Redirection
    protected function redirect(string $path): void {
        $path = $this->normalizeRedirectPath($path);
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    // Vérifier qu'une redirection reste interne à l'application
    protected function isInternalPath(string $path): bool {
        return $this->normalizeRedirectPath($path) !== '';
    }

    private function normalizeRedirectPath(string $path): string {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        $parts = parse_url($path);
        if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) {
            return '';
        }

        $path = $parts['path'] ?? '';
        $path = preg_replace('#\.{2}[/\\]#', '', $path);
        $path = preg_replace('#[^a-zA-Z0-9/_\-\.\?=&]#', '', $path);

        $query = '';
        if (!empty($parts['query'])) {
            $query = '?' . preg_replace('#[^a-zA-Z0-9/_\-\.\?=&]#', '', $parts['query']);
        }

        return $path . $query;
    }

    // Réponse JSON (pour les appels AJAX)
    protected function json(array $data, int $status = 200): void {
        if (ob_get_length() !== false && ob_get_length() > 0) {
            ob_clean();
        }
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = json_encode([
                'success' => false,
                'error' => 'Erreur interne : impossible de sérialiser la réponse JSON.'
            ], JSON_UNESCAPED_UNICODE);
            if ($json === false) {
                $json = '{"success":false,"error":"Erreur JSON critique"}';
            }
        }
        echo $json;
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
            if ($this->wantsJson()) {
                $this->json(['success' => false, 'error' => 'Jeton CSRF invalide. Veuillez réessayer.'], 403);
            }
            Session::flash('error', 'Jeton CSRF invalide. Veuillez réessayer.');
            $this->redirect('dashboard');
        }
    }

    // Détecte si la requête attend une réponse JSON
    protected function wantsJson(): bool {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($uri, PHP_URL_PATH) ?: '';
        $isApiPath = stripos($path, '/api/') !== false;
        return $xhr || stripos($accept, 'application/json') !== false || $isApiPath;
    }

// Méthode validate : gère validate. 
    protected function validate(array $data, array $rules): array {
        return Validator::validate($data, $rules);
    }

// Méthode abortValidation : gère abortValidation. 
    protected function abortValidation(array $errors, string $redirectPath): void {
        if (!empty($errors)) {
            Session::flash('error', implode(' ', $errors));
            $this->redirect($redirectPath);
        }
    }
}
