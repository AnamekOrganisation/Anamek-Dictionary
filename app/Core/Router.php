<?php

namespace App\Core;

class Router {
    private $routes = [];
    private $notFoundCallback;

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    private function addRoute($method, $path, $callback) {
        // Convert route parameters (e.g., {id}) to regex capture groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        // Escape forward slashes
        $pattern = str_replace('/', '\/', $pattern);
        // Add start and end delimiters
        $pattern = '/^' . $pattern . '$/';

        $this->routes[$method][$pattern] = $callback;
    }

    public function setNotFound($callback) {
        $this->notFoundCallback = $callback;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        if ($uri === false || $uri === '') {
            $uri = '/';
        }

        // Support subdirectory installation
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $base = str_replace('/index.php', '', $scriptName);
        
        if ($base !== '' && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }

        // Remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        if ($uri === '') {
            $uri = '/';
        }

        if (!isset($this->routes[$method])) {
            $this->handleNotFound();
            return;
        }

        foreach ($this->routes[$method] as $pattern => $callback) {
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func($callback, $params);
                return;
            }
        }

        $this->handleNotFound();
    }

    private function handleNotFound() {
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found";
        }
    }
}
