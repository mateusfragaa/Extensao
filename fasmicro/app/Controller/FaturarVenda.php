<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use Core\Library\Session;
use App\Service\PedidoVenda;

class FaturarVenda extends ControllerMain
{
    private $serviceVenda;

    public function __construct()
    {   
        $this->loadHelper(['formHelper','vendaHelper']);
        $this->serviceVenda = new PedidoVenda();
        return parent::__construct();
    }

    public function index(bool $temFiltro)
    {
            $this->view(
                'admin/listaVenda',
                [
                    "vendas" => $this->model->lista('pev_status'),
                ],
                'sistema'
            );
    }

    public function formVenda($acao,$id)
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
            case 'view':
                $data["action_form"] = "view";
                break;
        }

        $this->view(
            'admin/form/formVenda',
            [
                "data" => $data
            ],
            'sistema'
        );
    }

    // =====================================================================
    // Tratar o formulário da esquerda do formVenda.php
    public function update($action, $id_pedido)
    {
        if ($this->serviceVenda->updateVenda($_POST, $id_pedido)) {
            Redirect::page("venda/editandoVenda/form/$id_pedido", ['msgSucesso' => 'Sucesso ao atualizar o registro']);
        } else {
            Redirect::page("venda/", ['msgError' => 'Erro ao atualizar registro, verifique se os dados estão corretos!']);
        }
    }

    public function delete()
    {
        if ($this->model->delete($_POST)) {
            Redirect::page("venda/", ['msgSucesso' => 'Sucesso ao apagar o registro']);
        } else {
            Redirect::page("venda/", ['msgError' => 'Erro ao apagar registro, verifique se os dados estão corretos!']);
        }
    }

    public function insert()
    {
        $idGerado = $this->model->insert($_POST);
        if ($idGerado) {
            Redirect::page('venda/',['msgSucesso' => 'Sucesso ao inserir registro, nova venda']);
        }else {
            Redirect::page('venda/', ['msgError' => 'Erro ao inserir registro, verifique se os dados estão corretos!']);
        }
    }
}