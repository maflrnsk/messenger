<?php
namespace App\Router;
class Router {
    private $routes = [];

    public function add($method, $path, $callback) {
        $this->routes[] = compact('method', 'path', 'callback');
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($method === $route['method'] && $path === $route['path']) {
                // Если callback задан как массив класса и метода
                if (is_array($route['callback'])) {
                    [$class, $method] = $route['callback'];
                    $instance = new $class();
                    call_user_func([$instance, $method]);
                } else {
                    call_user_func($route['callback']);
                }
                return;
            }
        }
        http_response_code(404);
        echo "Page not found";
    }
}