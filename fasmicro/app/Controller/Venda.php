<?php

namespace App\Controller;

use App\Service\PedidoVenda;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use Core\Library\Session;

class Venda extends ControllerMain
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
        $data = [];
        if (!$temFiltro) {
            $data["vendas"] = $this->model->listaVenda();
            $data["status_venda"] = $this->serviceVenda->getStatusVenda();
            $this->view(
                'admin/listaVenda',
                [
                    "data" => $data
                ],
                'sistema'
            );
        }else {
            $data["vendas"] = $this->model->filtroListagem($_POST);
            $data["status_venda"] = $this->serviceVenda->getStatusVenda();
            $this->view(
                'admin/listaVenda',
                [
                    "data" => $data
                ],
                'sistema'
            );
        }
    }

    public function filtroListagemVenda()
    {
        $this->index(true);
    }

    // =====================================================================
    // Prepara o formulário da esquerda do formVenda.php (devo usar na tabela da grid)
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
        // Não vai ter como criar venda manual, vai ser criado automaticamente assim que adionar um item e enviar
        /*
                1- Receber os dados do post
                2- Com base na ação eu altero a venda do id
                3- Para update eu preciso carregar
        */

        if ($acao == 'insert') {
            $data['produtos'] = $this->serviceVenda->listaProduto('prd_descricao');
            $data['pessoas'] = $this->serviceVenda->listaPessoa('pes_nome');
            $data['status_venda'] = $this->serviceVenda->getStatusVenda();
        } else {
            die('carrega a venda id');
            $data["vendas"] = $this->model->getById($id);
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

    // =====================================================================
    // Formulário da direita
    public function inicioVenda()
    {   
        $data = [];
        $data['produtos_pedidos'] = $this->serviceVenda->comecarPedidoVenda($_POST);
        $data['produtos'] = $this->serviceVenda->listaProduto('prd_descricao');
        $data['pessoas'] = $this->serviceVenda->listaPessoa('pes_nome');
        $data['info_venda'] = $this->serviceVenda->getVenda(Session::getDestroy('id_pedido_editando'));
        $data['status_venda'] = $this->serviceVenda->getStatusVenda();
        $data['action_form_modal'] = 'editandoVenda';
        $data['action_form'] = 'update';

        $this->view(
            'admin/form/formVenda',
            [
                "data" => $data
            ],
            'sistema'
        );   
    }

    // =====================================================================
    public function calculaTotalVenda() {
        $data = json_decode(file_get_contents('php://input'), true);
        $novoTotal = $this->serviceVenda->calcularTotal($data['acrescimo'], $data['desconto'], $data['venda']);
        echo json_encode($novoTotal);
    }

    // =====================================================================
    public function editandoVenda($action = '', $id_pedido = 0)
    {
        $data = [];
        if ($action == 'modal') {
            $this->serviceVenda->addProdutoPedido($id_pedido, $_POST);
        }

        $data['produtos_pedidos'] = $this->serviceVenda->select_produto_venda($id_pedido);
        $data['produtos'] = $this->serviceVenda->listaProduto('prd_descricao');
        $data['pessoas'] = $this->serviceVenda->listaPessoa('pes_nome');
        $data['info_venda'] = $this->serviceVenda->getVenda($id_pedido);
        $data['status_venda'] = $this->serviceVenda->getStatusVenda();
        $data['action_form_modal'] = 'editandoVenda';
        $data['action_form'] = 'update';
        

        $this->view(
            'admin/form/formVenda',
            [
                "data" => $data
            ],
            'sistema'
        );
    }   

    public function excluirProduto()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($this->serviceVenda->excluirProduto($data['produtos_excluir']));
    }
}

