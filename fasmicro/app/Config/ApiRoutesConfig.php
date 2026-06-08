<?php

use Core\Library\ApiRoutes;

/**
 * Registro de rotas da API REST.
 *
 * Este arquivo é carregado automaticamente pelo Routes::dispatchApi()
 * sempre que a URL começa com /api/.
 *
 * Formato:
 *   ApiRoutes::<método>('<recurso>', '<Controller>', '<método do controller>');
 *
 * O controller deve:
 *   - Estar em app/Controller/Api/
 *   - Estender App\Controller\Api\ApiControllerMain
 *   - Receber array $params como primeiro argumento
 *
 * Exemplos de URL gerada:
 *   GET    http://seusite/api/v1/auth/login
 *   GET    http://seusite/api/v1/produtos
 *   GET    http://seusite/api/v1/produtos/5
 *   POST   http://seusite/api/v1/produtos
 *   PUT    http://seusite/api/v1/produtos/5
 *   DELETE http://seusite/api/v1/produtos/5
 */

// ------------------------------------------------------------------
// Autenticação
// ------------------------------------------------------------------
ApiRoutes::post('auth/login',   'Api\AuthApiController', 'login');
ApiRoutes::post('auth/refresh', 'Api\AuthApiController', 'refresh');
ApiRoutes::get('auth/me',       'Api\AuthApiController', 'me');

// ------------------------------------------------------------------
// Seus recursos aqui
// Descomente e adapte conforme criar os controllers de API
// ------------------------------------------------------------------

ApiRoutes::get('produtos',           'Api\ProdutoApi', 'index');
ApiRoutes::get('produtos/{id}',      'Api\ProdutoApi', 'show');
ApiRoutes::post('produtos',          'Api\ProdutoApi', 'store');
ApiRoutes::put('produtos/{id}',      'Api\ProdutoApi', 'update');
ApiRoutes::patch('produtos/{id}',    'Api\ProdutoApi', 'patch');
ApiRoutes::delete('produtos/{id}',   'Api\ProdutoApi', 'destroy');
