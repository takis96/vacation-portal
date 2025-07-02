<?php
class Router
{
    private array $routes = [];

    //Registers a new route by HTTP method and URL pattern, associating it with a callback to handle matching requests.
    public function add(string $method, string $pattern, callable $callback): void
    {
        $this->routes[] = compact('method', 'pattern', 'callback');
    }

    //Matches the incoming HTTP request to a registered route and executes its callback or returns a 404 if no match is found.
    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes as $route) {
            if ($method !== $route['method']) continue;
            //anchors the pattern to match the entire URL path; '{param}' is replaced with '(?P<param>[^/]+)' to capture dynamic URL segments by name.
            $regex = "@^" . preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $route['pattern']) . "$@";
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                //Calls the specified callback function with an array of parameters (e.g., extracted URL params) passed as arguments.
                call_user_func_array($route['callback'], $params);
                return;
            }
        }
        Response::json(['message' => 'Not Found'], 404);
    }
}
