<?php
// Router sederhana (pengganti routing framework)
class Router
{
    private static array $routes = [];

    public static function get(string $p, $h, array $m = []): void    { self::$routes[] = ['GET', $p, $h, $m]; }
    public static function post(string $p, $h, array $m = []): void   { self::$routes[] = ['POST', $p, $h, $m]; }
    public static function put(string $p, $h, array $m = []): void    { self::$routes[] = ['PUT', $p, $h, $m]; }
    public static function delete(string $p, $h, array $m = []): void { self::$routes[] = ['DELETE', $p, $h, $m]; }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST' && isset($_POST['_method'])) {
            $o = strtoupper($_POST['_method']);
            if (in_array($o, ['PUT', 'DELETE'], true)) $method = $o;
        }

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $base = base_path();
        if ($base !== '' && strpos($path, $base) === 0) $path = substr($path, strlen($base));
        $path = '/' . trim($path, '/');
        if ($path === '/index.php') $path = '/';

        foreach (self::$routes as [$m, $pattern, $handler, $middlewares]) {
            if ($m !== $method) continue;
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (!preg_match($regex, $path, $match)) continue;

            if ($method !== 'GET') verify_csrf();
            foreach ($middlewares as $mw) call_user_func($mw);

            $params = array_values(array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY));
            call_user_func_array($handler, $params);
            return;
        }
        abort(404);
    }
}
