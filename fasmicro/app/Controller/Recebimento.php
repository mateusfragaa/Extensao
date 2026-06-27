<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;

class Recebimento extends ControllerMain
{
    public function __construct()
    {
        $this->loadHelper(['formHelper']);
        return parent::__construct();
    }

    public function index()
    {
        $data = [];
        $data["recebimentos"] = $this->model->buscar_recebimento_completo();
        $data["status_rec"] = $this->model->getStatus();

        $this->view(
            'admin/listaRecebimento',
            ['data' => $data],
            'sistema'
        );
    }

    public function bordero()
    {
        $this->view(
            'admin/form/formFinalizacaoRecebimento',
            [],
            'sistema'
        );
    }

    public function formRecebimento($acao, $id)
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
            'admin/form/formRecebimento',
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
            Redirect::page('produto/', ['msgSucesso' => 'Sucesso ao inserir registro, novo produto : ' . $idGerado]);
        } else {
            Redirect::page('produto/');
        }
    }
}
