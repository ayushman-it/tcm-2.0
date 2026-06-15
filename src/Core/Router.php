<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * Simple regex-free router with {param} placeholders.
 */
final class Router
{
    /** @var list<array{method:string,pattern:string,handler:callable|array}> */
    private array $routes = [];

    public function get(string $pattern, callable|array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable|array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable|array $handler): void
    {
        $this->add('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, callable|array $handler): void
    {
        $this->add('DELETE', $pattern, $handler);
    }

    /**
     * Register a handler that responds to any of the listed methods.
     *
     * @param list<string> $methods
     */
    public function match(array $methods, string $pattern, callable|array $handler): void
    {
        foreach ($methods as $method) {
            $this->add(strtoupper($method), $pattern, $handler);
        }
    }

    private function add(string $method, string $pattern, callable|array $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => '/' . trim($pattern, '/'),
            'handler' => $handler,
        ];
    }

    /**
     * Dispatch the current request to a matching route.
     */
    public function dispatch(string $method, string $path): void
    {
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $params = $this->matchPath($route['pattern'], $path);
            if ($params !== null) {
                $this->invoke($route['handler'], $params);
                return;
            }
        }

        $this->notFound();
    }

    /**
     * @return array<string,string>|null Matched params, or null if no match.
     */
    private function matchPath(string $pattern, string $path): ?array
    {
        $patternParts = explode('/', trim($pattern, '/'));
        $pathParts = explode('/', trim($path, '/'));

        if (count($patternParts) !== count($pathParts)) {
            return null;
        }

        $params = [];
        foreach ($patternParts as $i => $part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $params[trim($part, '{}')] = $pathParts[$i];
            } elseif ($part !== $pathParts[$i]) {
                return null;
            }
        }
        return $params;
    }

    /**
     * @param array<string,string> $params
     */
    private function invoke(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $action] = $handler;
            $controller = new $class();
            $controller->$action($params);
            return;
        }
        $handler($params);
    }

    private function notFound(): never
    {
        if (Request::isJson()) {
            Response::error('Resource not found.', 404);
        }
        http_response_code(404);
        View::render('errors/404', [], 'public');
        exit;
    }
}
