<?php

namespace Core\Library;

/**
 * Formato de URL: /api/v{n}/{recurso}/{id}
 *
 * Registra rotas assim:
 *   ApiRoutes::get('produtos',          'Api\ProdutoApi', 'index');
 *   ApiRoutes::get('produtos/{id}',     'Api\ProdutoApi', 'show');
 *   ApiRoutes::post('produtos',         'Api\ProdutoApi', 'store');
 *   ApiRoutes::put('produtos/{id}',     'Api\ProdutoApi', 'update');
 *   ApiRoutes::delete('produtos/{id}',  'Api\ProdutoApi', 'destroy');
 */
class ApiRoutes
{
    /** @var array[] Tabela de rotas registradas */
    private static array $routes = [];

    /**
     * get
     *
     * @param string $pattern
     * @param string $controller
     * @param string $method
     * @return void
     */
    public static function get(string $pattern, string $controller, string $method): void
    {
        self::add('GET', $pattern, $controller, $method);
    }

    /**
     * post
     *
     * @param string $pattern
     * @param string $controller
     * @param string $method
     * @return void
     */
    public static function post(string $pattern, string $controller, string $method): void
    {
        self::add('POST', $pattern, $controller, $method);
    }

    /**
     * put
     *
     * @param string $pattern
     * @param string $controller
     * @param string $method
     * @return void
     */
    public static function put(string $pattern, string $controller, string $method): void
    {
        self::add('PUT', $pattern, $controller, $method);
    }

    /**
     * patch
     *
     * @param string $pattern
     * @param string $controller
     * @param string $method
     * @return void
     */
    public static function patch(string $pattern, string $controller, string $method): void
    {
        self::add('PATCH', $pattern, $controller, $method);
    }

    /**
     * delete
     *
     * @param string $pattern
     * @param string $controller
     * @param string $method
     * @return void
     */
    public static function delete(string $pattern, string $controller, string $method): void
    {
        self::add('DELETE', $pattern, $controller, $method);
    }

    /**
     * add
     *
     * @param string $httpMethod
     * @param string $pattern
     * @param string $controller
     * @param string $method
     * @return void
     */
    private static function add(string $httpMethod, string $pattern, string $controller, string $method): void
    {
        self::$routes[] = [
            'httpMethod'  => strtoupper($httpMethod),
            'pattern'     => trim($pattern, '/'),
            'controller'  => $controller,
            'method'      => $method,
        ];
    }

    /**
     * dispatch - Resolve a URL da requisição atual contra as rotas registradas e executa.
     *
     * @param string $uri Caminho da requisição a partir de /api/v{n}/ (ex: "produtos/5")
     */
    public static function dispatch(string $uri): void
    {
        self::setCorsHeaders();

        $httpMethod = self::resolveHttpMethod();
        $uri        = trim($uri, '/');

        if ($httpMethod === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        foreach (self::$routes as $route) {
            $params = self::match($route['pattern'], $uri);

            if ($params === null) {
                continue;
            }

            if ($route['httpMethod'] !== $httpMethod) {
                // Padrão bate mas método não — continua buscando
                continue;
            }

            self::execute($route['controller'], $route['method'], $params);
            return;
        }

        // Verifica se existe a rota com outro método (405 vs 404)
        foreach (self::$routes as $route) {
            if (self::match($route['pattern'], $uri) !== null) {
                ApiResponse::methodNotAllowed("Método $httpMethod não permitido para este recurso");
            }
        }

        ApiResponse::notFound("Rota não encontrada: $httpMethod /$uri");
    }

    /**
     * match - Compara o padrão de rota com a URI. Retorna array de params ou null.
     *
     * Suporta segmentos nomeados: {id}, {slug}, etc.
     */
    private static function match(string $pattern, string $uri): ?array
    {
        $patternParts = explode('/', $pattern);
        $uriParts     = explode('/', $uri);

        if (count($patternParts) !== count($uriParts)) {
            return null;
        }

        $params = [];

        foreach ($patternParts as $i => $segment) {
            if (preg_match('/^\{(\w+)\}$/', $segment, $m)) {
                $params[$m[1]] = $uriParts[$i];
            } elseif ($segment !== $uriParts[$i]) {
                return null;
            }
        }

        return $params;
    }

    /**
     * execute - Instancia o controller e chama o método passando params + request body.
     *
     * @param string $controllerName
     * @param string $methodName
     * @param array $params
     * @return void
     */
    private static function execute(string $controllerName, string $methodName, array $params): void
    {
        $class = "App\\Controller\\$controllerName";

        if (!class_exists($class)) {
            ApiResponse::serverError("Controller '$controllerName' não encontrado");
        }

        $instance = new $class();

        if (!method_exists($instance, $methodName)) {
            ApiResponse::serverError("Método '$methodName' não encontrado em '$controllerName'");
        }

        // Disponibiliza parâmetros de rota via $_GET para compatibilidade
        foreach ($params as $key => $value) {
            $_GET[$key] = $value;
        }

        $instance->$methodName($params);
    }

    /**
     * resolveHttpMethod - Detecta o método HTTP real, com suporte a override via header ou campo de form.
     *
     * @return string
     */
    private static function resolveHttpMethod(): string
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Suporte a browsers/proxies que só enviam POST
        if ($method === 'POST') {
            $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']
                ?? $_POST['_method']
                ?? '';

            $override = strtoupper(trim($override));

            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                return $override;
            }
        }

        return $method;
    }

    /**
     * setCorsHeaders - Define os headers CORS para todas as respostas de API
     *
     * @return void
     */
    private static function setCorsHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        $allowedOrigin = Ambiente::get('API_CORS_ORIGIN') ?: '*';

        header("Access-Control-Allow-Origin: $allowedOrigin");
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-HTTP-Method-Override');
        header('Access-Control-Max-Age: 86400');
    }
}
