<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;

class Pessoa extends ControllerMain
{
    public function __construct()
    {   
        $this->loadHelper('formHelper');
        return parent::__construct();
    }

    public function index(bool $temFiltro = false)
    {
        if (!$temFiltro) {
            $this->view(
                'admin/listaPessoa',
                [
                    "pessoas" => $this->model->lista('PES_NOME')
                ],
                'sistema'
            );
        } else {
            $this->view(
                'admin/listaPessoa',
                [
                    "pessoas" => $this->model->filtroListagem($_POST)
                ],
                'sistema'
            );
        }
    }

    public function filtroListagemPessoa()
    {
        $this->index(true);
    }

    /**
     * Prepara o formulário para as ações e faz o require do mesmo
     */
    public function formPessoa($acao = 'view', $id = null)
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

        if ($acao != 'insert' && $id !== null) {
            $data["pessoa"] = $this->model->getById($id);
        }

        $this->view(
            'admin/form/formPessoa',
            [
                "data" => $data
            ],
            'sistema'
        );
    }

    public function update()
    {
        if ($this->model->update($_POST)) {
            Redirect::page("pessoa/", ['msgSucesso' => 'Sucesso ao atualizar o registro']);
        } else {
            Redirect::page("pessoa/", ['msgError' => 'Erro ao atualizar registro, verifique se os dados estão corretos!']);
        }
    }

    public function delete()
    {
        if ($this->model->delete($_POST)) {
            Redirect::page("pessoa/", ['msgSucesso' => 'Sucesso ao apagar o registro']);
        } else {
            Redirect::page("pessoa/", ['msgError' => 'Erro ao apagar registro, verifique se os dados estão corretos!']);
        }
    }

    public function insert()
    {
        $idGerado = $this->model->insert($_POST);
        if ($idGerado) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Sucesso ao inserir registro, nova pessoa: ' . $idGerado]);
        } else {
            Redirect::page('pessoa/', ['msgError' => 'Erro ao inserir registro, verifique se os dados estão corretos!']);
        }
    }
}
