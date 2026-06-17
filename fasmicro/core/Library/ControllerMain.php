<?php

namespace Core\Library;

use Core\Library\Csrf;
use Core\Library\Redirect;
use Core\Library\Request;

class ControllerMain
{
    protected $controller;
    protected $method;
    protected $action;
    protected $request;
    protected $template;

    public $model;

    use RequestTrait;
    use ModelLoaderTrait;   // ← padrão do professor (loadModel extraído para trait)

    /**
     * __construct
     */
    public function __construct()
    {
        $this->request      = new Request();
        $aParametros        = Self::getRotaParametros();
        $this->controller   = $aParametros['controller'];
        $this->method       = $aParametros['method'];
        $this->action       = $aParametros['action'];
        $this->template     = new Template();

        // Validação CSRF: rejeita requisições mutantes sem token válido
        if (CSRF_PROTECTION && !Csrf::isExcluded()) {
            $httpMethod = $this->request->getHttpMethod();
            if (in_array($httpMethod, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $headerVar = 'HTTP_' . strtoupper(str_replace('-', '_', CSRF_HEADER_NAME));
                $token     = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER[$headerVar] ?? null;
                if (!Csrf::validate($token)) {
                    http_response_code(419);
                    return Redirect::page('Home/viewErros', ['msgError' => 'Token de segurança inválido. Recarregue a página e tente novamente.']);
                }
            }
        }

        // Carregamento de helpers globais (padrão do professor)
        $this->loadHelper(['url', 'data', 'formHelper']);

        // Carregamento do model default do controller
        $this->model = $this->loadModel($this->controller);

        // Verificação de autenticação
        // NOTA: O login do projeto de extensão ainda não implementa sessão userId.
        // Quando o grupo implementar a autenticação real, basta remover o Auth
        // e HomeSistema da lista CONTROLLER_AUTH em Constants.php.
        if (!in_array($this->controller, CONTROLLER_AUTH)) {
            if (!Session::get('userId')) {
                return Redirect::page('Home/viewErros', ['msgError' => 'Para acessar o sistema, faça login primeiro.']);
            }
        }
    }

    /**
     * validaNivelAcesso
     * Redireciona se o nível do usuário logado for maior que o mínimo exigido.
     * Níveis: 1 = Super Admin, 11 = Admin, 21 = Usuário comum.
     *
     * @param int $nivelMinimo
     */
    public function validaNivelAcesso(int $nivelMinimo = 20): void
    {
        if ((int) Session::get('userNivel') > $nivelMinimo) {
            Redirect::page('Home/viewErros', ['msgError' => 'Você não possui permissão para acessar esta funcionalidade.']);
        }
    }

    /**
     * view
     */
    public function view(string $view, array $data = [], ?string $layout = null)
    {
        $this->template->render($view, $data, $layout);
    }

    /**
     * loadHelper
     */
    public function loadHelper($nomeHelper)
    {
        if (gettype($nomeHelper) == 'string') {
            $nomeHelper = [$nomeHelper];
        }

        foreach ($nomeHelper as $value) {
            $pathHelpCore = PATHAPP . 'core' . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR . "{$value}.php";

            if (file_exists($pathHelpCore)) {
                require_once $pathHelpCore;
            } else {
                $pathHelpApp = PATHAPP . 'app' . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR . "{$value}.php";
                if (file_exists($pathHelpApp)) {
                    require_once $pathHelpApp;
                }
            }
        }
    }
}
