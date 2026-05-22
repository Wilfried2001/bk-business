<?php
// ============================================================
//  core/Router.php — Routeur MVC
// ============================================================
class Router {

    private array $routes = [];

    // Enregistrer une route GET
    public function get(string $path, string $controller, string $method, array $roles = []): void {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method, 'roles' => $roles];
    }

    // Enregistrer une route POST
    public function post(string $path, string $controller, string $method, array $roles = []): void {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method, 'roles' => $roles];
    }

    // Résoudre la route courante
    public function resolve(): void {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri        = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Retirer le préfixe de BASE_URL du chemin
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if (!empty($basePath)) {
            $uri = str_replace($basePath, '', $uri);
        }
        $uri = '/' . ltrim($uri, '/');

        // Cherche une correspondance exacte
        if (isset($this->routes[$httpMethod][$uri])) {
            $this->dispatch($this->routes[$httpMethod][$uri], []);
            return;
        }

        // Cherche une route dynamique avec paramètres (:id, :slug, etc.)
        foreach ($this->routes[$httpMethod] ?? [] as $route => $action) {
            $pattern = preg_replace('/:([a-zA-Z_]+)/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->dispatch($action, $matches);
                return;
            }
        }

        // 404
        http_response_code(404);
        require_once VIEWS_PATH . '/layouts/header.php';
        echo '<div class="container mt-5 text-center">'
             . '<h1 class="display-1">404</h1>'
             . '<p class="lead">Page introuvable.</p>'
             . '<a href="' . BASE_URL . '/dashboard" class="btn btn-primary">Retour au tableau de bord</a>'
             . '</div>';
        require_once VIEWS_PATH . '/layouts/footer.php';
    }

    private function dispatch(array $action, array $params): void {
        $roles = $action['roles'] ?? [];
        $this->authorize($roles);

        $controllerName = $action['controller'] . 'Controller';
        $method         = $action['method'];
        $controller = new $controllerName();
        call_user_func_array([$controller, $method], $params);
    }

    private function authorize(array $roles): void {
        if (empty($roles)) {
            return;
        }

        if ($roles === ['*']) {
            Auth::requireAuth();
            return;
        }

        Auth::requireRole($roles);
    }
}
