<?php

namespace Core\Library;

class ControllerMain
{
    protected $controller;
    protected $method;
    protected $action;
    protected $request;
    protected $template;
    
    public $model;

    use RequestTrait;

    /**
     * __construct
     */
    public function __construct()
    {
        $aParametros        = Self::getRotaParametros();
        $this->controller   = $aParametros['controller'];
        $this->method       = $aParametros['method'];
        $this->action       = $aParametros['action'];
        $this->template     = new Template();
        $this->request      = new Request();

        // Carregamento de model default do controller
        $this->model        = $this->loadModel($this->controller);

        // Carregamento de helpers
        $this->loadHelper(['url', 'data']);
        // Verificação de permissão dos controllers autorizados sem login
    }

    /**
     * loadModel
     *
     * @param string $nomeModel
     * @return void|object
     */
    public function loadModel(string $nomeModel)
    {
        $pathModel = 'App\model\\' . $nomeModel . "Model";

        if (class_exists($pathModel)) {
            return new $pathModel();
        }
    }

    /**
     * loadService
     *
     * @param string $nomeService
     * @return void|object
     */
    public function loadService(string $nomeService)
    {
        $pathService = 'App\Service\\' . $nomeService . "Service";

        if (class_exists($pathService)) {
            return new $pathService();
        }
    }

    /**
     * view
     * 
     * Exemplo: $this->view("admin/listaProduto", ['titulo' => 'Lista de Produtos'])
     *
     * @param string $view
     * @param array $data
     * @param string|null $layout
     * @return void
     */
    public function view(string $view, array $data = [], ?string $layout = null)
    {
        $this->template->render($view, $data, $layout);
    }

    /**
     * Undocumented function
     *
     * @param string|array $nomeHelper
     * @return void
     */
    public function loadHelper($nomeHelper)
    {
        if (gettype($nomeHelper) == "string") {
            $nomeHelper = [$nomeHelper];
        }

        foreach ($nomeHelper as $value) {
            $pathHelpCore = PATHAPP . "core" . DIRECTORY_SEPARATOR . "Helper" . DIRECTORY_SEPARATOR . "{$value}.php";

            if (file_exists($pathHelpCore)) {
                require_once $pathHelpCore;
            } else {
                $pathHelpApp = PATHAPP . "app" . DIRECTORY_SEPARATOR . "Helper" . DIRECTORY_SEPARATOR . "{$value}.php";

                if (file_exists($pathHelpApp)) {
                    require_once $pathHelpApp;
                }
            }
        }
    }
}