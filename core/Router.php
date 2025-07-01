<?php
class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $callback): void
    {
        $this->routes[] = compact('method', 'pattern', 'callback');
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes as $route) {
            if ($method !== $route['method']) continue;
            $regex = "@^" . preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $route['pattern']) . "$@";
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func_array($route['callback'], $params);
                return;
            }
        }
        Response::json(['message' => 'Not Found'], 404);
    }
}
