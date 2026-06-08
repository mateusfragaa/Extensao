<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use Core\Library\Session;


class Pessoa extends ControllerMain
{
    public function __construct()
    {
        $this->loadHelper('formHelper');
        return parent::__construct();
    }

    public function index($action = null, $id = null)
    {
        $this->view(
            'admin/listaPessoa',
            [
                "pessoas" => $this->model->lista('PES_NOME')
            ],
            'sistema'
        );
    }

    public function filtroListagemPessoa($action = null, $id = null)
    {
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
        $layout = $isAjax ? null : 'sistema';

        $this->view(
            'admin/listaPessoa',
            [
                "pessoas" => $this->model->filtroListagem($_POST)
            ],
            $layout
        );
    }

    /**
     * Prepara o formulário para as ações e faz o require do mesmo
     */
    public function formPessoa($acao = 'view', $id = null)
    {
        $data = [];

        // Se houver erros de validação na sessão, recupera os dados digitados
        $formInputs = Session::getDestroy('formInputs');

        if ($acao == 'insert') {
            $data["action_form"] = "insert";
            $data["pessoa"] = $formInputs ?: [];
        } else {
            $data["action_form"] = $acao;
            // Se falhou a validação no update, usa o que o usuário digitou, senão busca do banco
            $data["pessoa"] = $formInputs ?: $this->model->getById($id);
        }

        $this->view(
            'admin/form/formPessoa',
            [
                "data" => $data
            ],
            'sistema'
        );
    }

    public function update($action = null, $id = null)
    {
        if ($this->model->update($_POST)) {
            Redirect::page("pessoa/", ['msgSucesso' => 'Sucesso ao atualizar o registro']);
        } else {
            $msgError = Session::get('msgError') ?: 'Erro ao atualizar registro, verifique os dados!';
            Redirect::page("pessoa/formPessoa/update/" . $_POST['PES_ID'], ['msgError' => $msgError]);
        }
    }

    public function delete($action = null, $id = null)
    {
        if ($this->model->delete($_POST)) {
            Redirect::page("pessoa/", ['msgSucesso' => 'Sucesso ao apagar o registro']);
        } else {
            Redirect::page("pessoa/", ['msgError' => 'Erro ao apagar registro!']);
        }
    }
    public function insert($action = null, $id = null)
    {
        // Remove o PES_ID para que o banco de dados gere o ID automaticamente (AUTO_INCREMENT)
        if (isset($_POST['PES_ID'])) {
            unset($_POST['PES_ID']);
        }

        $idGerado = $this->model->insert($_POST);

        if ($idGerado) {
            Redirect::page('pessoa/', ['msgSucesso' => 'Sucesso ao inserir registro, nova pessoa: ' . $idGerado]);
        } else {
            $msgError = Session::get('msgError') ?: 'Erro ao inserir registro, verifique os campos obrigatórios!';
            Redirect::page('pessoa/formPessoa/insert', ['msgError' => $msgError]);
        }
    }
}