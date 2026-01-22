<?php

class Router {
    protected $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch($uri, $method) {
        $uri = parse_url($uri, PHP_URL_PATH);
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        // Exact match
        if (isset($this->routes[$method][$uri])) {
            return call_user_func($this->routes[$method][$uri]);
        }

        // Regex match
        foreach ($this->routes[$method] as $route => $callback) {
            if (strpos($route, '{') === false) continue; // Optimization: only check dynamic routes
            
            // Convert /place/{id} to regex /place/(\d+)
            $pattern = "@^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_\-]+)', $route) . "$@D";
            
            if (preg_match($pattern, $uri, $matches)) {
                // Remove numeric keys
                foreach ($matches as $key => $value) {
                    if (is_int($key)) unset($matches[$key]);
                }
                return call_user_func_array($callback, $matches);
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
