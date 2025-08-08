<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        // Remove query string da URI
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Remove trailing slash, exceto para root
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                $params = $this->extractParams($route['path'], $uri);
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        // Rota não encontrada
        http_response_code(404);
        include __DIR__ . '/../../views/errors/404.php';
    }

    private function matchPath(string $routePath, string $uri): bool
    {
        // Converter parâmetros da rota para regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
    }

    private function extractParams(string $routePath, string $uri): array
    {
        $params = [];
        
        // Extrair nomes dos parâmetros
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
        
        // Extrair valores dos parâmetros
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove a match completa
            
            foreach ($paramNames[1] as $index => $paramName) {
                $params[$paramName] = $matches[$index] ?? null;
            }
        }
        
        return $params;
    }

    private function callHandler(string $handler, array $params): void
    {
        list($controllerName, $method) = explode('@', $handler);
        
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} não encontrado");
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            throw new \Exception("Método {$method} não encontrado no controller {$controllerClass}");
        }
        
        call_user_func_array([$controller, $method], $params);
    }
}
