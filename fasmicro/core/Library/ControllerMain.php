<?php

namespace Core\Library;

use Core\Library\Csrf;
use Core\Library\Request;

class ControllerMain
{
    protected $controller;
    protected $method;
    protected $action;
    protected $request;        // ← agora é uma instância de Request
    protected $template;

    public $model;

    use RequestTrait;

    /**
     * __construct
     * CORREÇÃO: $this->request não era instanciado → getHttpMethod() explodia em null.
     */
    public function __construct()
    {
        // Instancia o objeto Request ANTES de qualquer uso de $this->request
        $this->request = new Request();

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

        // Carregamento do model padrão do controller
        $this->model = $this->loadModel($this->controller);

        // Carregamento de helpers globais
        $this->loadHelper(['url', 'data']);
    }

    /**
     * loadModel
     * CORREÇÃO: namespace era 'App\\model\\' (minúsculo) — em Linux isso não resolve.
     */
    public function loadModel(string $nomeModel)
    {
        $pathModel = 'App\\Model\\' . $nomeModel . 'Model';

        if (class_exists($pathModel)) {
            return new $pathModel();
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
