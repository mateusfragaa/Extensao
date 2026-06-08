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
    use ModelLoaderTrait;

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

        // Carregamento de helpers
        $this->loadHelper(['url', 'data', 'formHelper', 'jsHelper']);

        // Carregamento de model default do controller
        $this->model        = $this->loadModel($this->controller);

        // Verificação de permissão dos controllers autorizados sem login
        if (!in_array($this->controller, CONTROLLER_AUTH)) {
            if (!Session::get("userId")) {
                return Redirect::page("Home/viewErros", ['msgError' => "Para acessar a rotina favor antes efetuar o login."]);
            }
        }
    }

    /**
     * validaNivelAcesso
     *
     * @param int $nivelMinino 
     * @return void
     */
    public function validaNivelAcesso(int $nivelMinino = 20)
    {
        if (((int)Session::get("userNivel") > $nivelMinino)) {
            return Redirect::page("Home/viewErros", ["msgError" => "Você não possui permissão neste programa"]);
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
     * loadHelper
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

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return $this->view(
            "admin/lista" . $this->controller,
            [
                'titulo'    => $this->model->titulo,
                "lista"     => $this->model->lista(),
                "aStatus"   => $this->model->listaStatus 
            ]
        );
    }


    /**
     * form
     *
     * @param string $action
     * @param integer $id
     * @return void
     */
    public function form($action, $id = 0)
    {
        return $this->view(
            "admin/form" . $this->controller,
            [
                'titulo'    => $this->model->titulo,
                "data"      => $this->model->getById($id),
                "aStatus"   => $this->model->listaStatus,
                "action"    => $this->action
            ]
        ); 
    }

    /**
     * insert
     *
     * @return void
     */
    public function insert()
    {
        if ($this->model->insert($this->request->getPost())) {
            return Redirect::page(
                        $this->controller,
                        ['msgSucesso' => "Registro inserido com sucesso."]
                    );
        } else {
            return Redirect::page(
                    $this->controller. "/form/" . $this->method . "/0",
                    ['msgError' => "Falha ao inserir registro."]
                );
        }
    }

    /**
     * update
     *
     * @return void
     */
    public function update()
    {
        $post = $this->request->getPost();

        if ($this->model->update($post)) {
            return Redirect::page(
                    $this->controller,
                    ['msgSucesso' => "Registro atualizado com sucesso."]
                );
        } else {
            return Redirect::page(
                    $this->controller . '/form/' . $this->method . '/' . $post[$this->model->primaryKey],
                    ['msgError' => "Falha ao atualizar registro."]
                );
        }
    }

    /**
     * delete
     *
     * @return void
     */
    public function delete()
    {
        if ($this->model->delete($this->request->getPost())) {
            return Redirect::page(
                    $this->controller, 
                    ['msgSucesso' => "Registro excluído com sucesso."]
                );
        } else {
            return Redirect::page(
                    $this->controller, 
                    ['msgError' => "Falha ao excluír o registro."]
                );
        }
    }
}