<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;

class Produto extends ControllerMain
{
    public function __construct()
    {   
        $this->loadHelper('formHelper');
        return parent::__construct();
    }

    public function index(bool $temFiltro)
    {
        if (!$temFiltro) {
            $this->view(
                'admin/listaProduto',
                [
                    "produtos" => $this->model->lista('prd_descricao')
                ],
                'sistema'
            );
        }else {
            $this->view(
                'admin/listaProduto',
                [
                    "produtos" => $this->model->filtroListagem($_POST)
                ],
                'sistema'
            );
        }
    }

    public function filtroListagemProduto()
    {
        $this->index(true);
    }

    /*
    * Prepara o formulário para as ações e faz o require do mesmo
    */
    public function formProduto($acao,$id)
    {
        $data = [];
        switch ($acao) {
            case 'insert':
                $data["action_form"] = "insert";
                break;
            case 'update':
                $data["action_form"] = "update";
                break;
            case 'delete':
                $data["action_form"] = "delete";
                break;
            default:
                $data["action_form"] = "view";
                break;
        }

        if ($acao != 'insert') $data["produto"] = $this->model->getById($id);

        $this->view(
            'admin/form/formProduto',
            [
                "data" => $data
            ],
            'sistema'
        );
    }

    public function update()
    {
        if ($this->model->update2($_POST)) {
            Redirect::page("produto/", ['msgSucesso' => 'Sucesso ao atualizar o registro']);
        } else {
            Redirect::page("produto/", ['msgError' => 'Erro ao atualizar registro, verifique se os dados estão corretos!']);
        }
    }

    public function delete()
    {
        if ($this->model->delete($_POST)) {
            Redirect::page("produto/", ['msgSucesso' => 'Sucesso ao apagar o registro']);
        } else {
            Redirect::page("produto/", ['msgError' => 'Erro ao apagar registro, verifique se os dados estão corretos!']);
        }
    }

    public function insert()
    {
        $idGerado = $this->model->insert2($_POST);
        
        if ($idGerado) {
            Redirect::page('produto/',['msgSucesso' => 'Sucesso ao inserir registro, novo produto : ' . $idGerado]);
        }else {
            Redirect::page('produto/');
        }
    }
}
