<?php
namespace App\Core;

class Router {
    protected $routes = [];
    protected $csrfExcept = [
        'give/webhook',
    ];

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    protected function addRoute($method, $path, $callback) {
        $path = trim($path, '/');
        if ($path === '') $path = '/';
        $this->routes[$method][$path] = $callback;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Get script name to remove subfolder path
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        
        if ($scriptName !== '/' && strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }
        
        $uri = trim($uri, '/');
        if ($uri === '') $uri = '/'; // Root route

        if ($method === 'POST' && !$this->isCsrfExcepted($uri)) {
            Security::requireCsrf();
        }

        if ($method === 'POST' && strpos($uri, 'admin/') === 0 && isset($_SESSION['user_id'])) {
            \App\Models\AuditLog::record($method, $uri);
        }

        // Check for exact match
        if (isset($this->routes[$method][$uri])) {
            return $this->callAction($this->routes[$method][$uri]);
        }

        // Check for dynamic routes
        foreach ($this->routes[$method] as $route => $callback) {
            $routePattern = strpos($route, '(') !== false
                ? $route
                : preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $route);
            $pattern = "@^" . $routePattern . "$@D";
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                return $this->callAction($callback, $matches);
            }
        }

        // 404
        $this->sendNotFound();
    }

    protected function callAction($callback, $params = []) {
        if (is_array($callback)) {
            $controllerClass = $callback[0];
            $method = $callback[1];
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $method)) {
                    return call_user_func_array([$controller, $method], $params);
                }
            }
        }
        
        $this->sendNotFound();
    }

    protected function isCsrfExcepted($uri) {
        return in_array($uri, $this->csrfExcept, true);
    }
    
    protected function sendNotFound() {
        http_response_code(404);
        echo "404 - Page Not Found";
        exit;
    }
}
