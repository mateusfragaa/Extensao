<?php

namespace Core\Library;

class Routes
{
    use RequestTrait;

    /**
     * rota
     *
     * @return void
     */
    public static function rota()
    {
        $rawUri = isset($_REQUEST['parametros'])
            ? filter_var(rtrim($_REQUEST['parametros'], '/'), FILTER_SANITIZE_URL)
            : '';

        // Requisições para /api/ são despachadas pelo roteador de API
        if (str_starts_with(ltrim($rawUri, '/'), 'api/')) {
            self::dispatchApi($rawUri);
            return;
        }

        $pathContr   = "App\Controller\\";
        $aParametros = Self::getRotaParametros();
        $controller  = $pathContr . $aParametros['controller'];

        if (!class_exists($controller)) {
            Erros::controllerNotFound($aParametros['controller']);
        } else {
            if (!method_exists($controller, $aParametros['method'])) {
                Erros::methodNotFound($aParametros['method']);
            } else {
                $instance = new $controller();

                call_user_func_array([$instance, $aParametros['method']], array_merge([$aParametros['action'], $aParametros['id']], $aParametros['outrosPar']));

                return;
            }
        }
    }

    /**
     * Despacha requisições de API para o ApiRoutes após extrair a versão da URL.
     *
     * Formato esperado: api/v{n}/{recurso}/...
     * Exemplo: api/v1/produtos/5  →  ApiRoutes::dispatch('produtos/5')
     */
    private static function dispatchApi(string $rawUri): void
    {
        // Carrega as rotas da API registradas pela aplicação
        $apiRoutesFile = dirname(__DIR__, 2) . '/app/Config/ApiRoutesConfig.php';

        if (file_exists($apiRoutesFile)) {
            require_once $apiRoutesFile;
        }

        // Remove prefixo "api/v{n}/" e extrai a URI do recurso
        $uri        = ltrim($rawUri, '/');
        $apiVersion = API_VERSION;

        // Aceita tanto "api/v1/..." quanto "api/..." (usa versão padrão)
        if (preg_match('#^api/v(\d+)/(.*)$#i', $uri, $m)) {
            $uri = $m[2]; // ex: "produtos/5"
        } elseif (preg_match('#^api/(.*)$#i', $uri, $m)) {
            $uri = $m[1];
        }

        ApiRoutes::dispatch($uri);
    }
}