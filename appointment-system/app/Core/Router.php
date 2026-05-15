<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $pattern = '@^' . preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $route['path']) . '$@';
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            [$class, $action] = $route['handler'];
            $controller = new $class();
            $controller->$action($params);
            return;
        }
        http_response_code(404);
        echo '404 - Sayfa bulunamadı';
    }
}
