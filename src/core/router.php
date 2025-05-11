<?php
namespace App\core;

class Router {
    /** @var array<string, array<string, callable|string>> */
    private array $routes = [];

    /**
     * Регистрирует маршрут с любым HTTP-методом
     *
     * @param string $method  GET|POST|PUT|… 
     * @param string $route   URI, может содержать {param}
     * @param callable|string $handler  либо колбэк, либо "Controller@method"
     */
    public function add(string $method, string $route, $handler): void {
        // Нормализуем маршрут: убираем конечный слеш (кроме корня)
        $route = rtrim($route, '/');
        $route = $route === '' ? '/' : $route;
        $this->routes[strtoupper($method)][$route] = $handler;
    }

    /** Синоним add('GET', …) */
    public function get(string $route, $handler): void {
        $this->add('GET', $route, $handler);
    }

    /** Синоним add('POST', …) */
    public function post(string $route, $handler): void {
        $this->add('POST', $route, $handler);
    }

    /** Обрабатывает текущий запрос */
    public function resolve(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path   = rtrim($path, '/') ?: '/';

        $routesForMethod = $this->routes[$method] ?? [];

        foreach ($routesForMethod as $routePattern => $handler) {
            // Конвертация "/orders/{id}" → "#^/orders/(?P<id>[^/]+)$#"
            $regex = preg_replace('#\{([\w]+)\}#', '(?P<\1>[^/]+)', $routePattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $path, $matches)) {
                // Выбираем только именованные группы
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                return $this->callHandler($handler, $params);
            }
        }

        // Если не нашли ни одного совпадения
        http_response_code(404);
        echo "404 Not Found";
    }

    /**
     * Вызывает обработчик: либо callable, либо "Controller@method"
     *
     * @param callable|string $handler
     * @param array<string,string> $params
     */
    protected function callHandler($handler, array $params): void {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            list($controllerName, $method) = explode('@', $handler, 2);
            $fqcn = "App\\controllers\\{$controllerName}";
            if (!class_exists($fqcn)) {
                throw new \RuntimeException("Controller {$fqcn} not found");
            }
            $controller = new $fqcn();
            if (!method_exists($controller, $method)) {
                throw new \RuntimeException("Method {$method} not found in controller {$fqcn}");
            }
            call_user_func_array([$controller, $method], $params);
            return;
        }

        throw new \InvalidArgumentException('Invalid route handler');
    }
}