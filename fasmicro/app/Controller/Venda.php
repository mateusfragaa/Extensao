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
    public function formVenda($acao,$id_pedido)
    { 
        $this->editandoVenda($acao, $id_pedido);
    }
    // =====================================================================

    // =====================================================================
    // Tratar o formulário da esquerda do formVenda.php
    public function update($action, $id_pedido)
    {
      
        if ($this->serviceVenda->updateVenda($_POST, $id_pedido)) {
            Redirect::page("venda/formVenda/update/$id_pedido", ['msgSucesso' => 'Sucesso ao atualizar o registro']);
        } else {
            Redirect::page("venda/formVenda/update/$id_pedido", ['msgError' => 'Erro ao atualizar registro, verifique se os dados estão corretos!']);
        }
    }

    public function delete($acao, $id)
    {
        if ($this->serviceVenda->apagarVendaEItens($id)) {
            Redirect::page("venda/", ['msgSucesso' => 'Sucesso ao apagar o registro']);
        } else {
            Redirect::page("venda/", ['msgError' => 'Erro ao apagar registro, verifique se os dados estão corretos!']);
        }
    }

    // teste
    public function carregaValorExclusao()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $venda = $this->serviceVenda->getVenda($data['pedido_id']);
        echo json_encode($venda);
    }

    // =====================================================================
    // Formulário da direita
    public function inicioVenda($action)
    {   
        $data = [];

        $data['produtos'] = $this->serviceVenda->listaProduto('prd_descricao');
        $data['pessoas'] = $this->serviceVenda->listaPessoa('pes_nome');
        $data['status_venda'] = $this->serviceVenda->getStatusVenda();

        $this->serviceVenda->comecarPedidoVenda($_POST);
        Redirect::page("venda/formVenda/update/". Session::getDestroy('id_pedido_editando'));
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
        if ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->serviceVenda->addProdutoPedido($id_pedido, $_POST);
        }
        
        $data['action_form'] = 'update';
        if ($action == 'insert') {
            $data['action_form_modal'] = 'inicioVenda/insert';
        }elseif($action == 'update') {
            $data['action_form_modal'] = '/formVenda/update';
        }elseif($action == 'delete') {
            $data['action_form'] = 'delete';
        }

        $data['produtos'] = $this->serviceVenda->listaProduto('prd_descricao');
        $data['produtos_pedidos'] = $this->serviceVenda->select_produto_venda($id_pedido);
        $data['pessoas'] = $this->serviceVenda->listaPessoa('pes_nome');
        $data['info_venda'] = $this->serviceVenda->getVenda($id_pedido);
        $data['status_venda'] = $this->serviceVenda->getStatusVenda();
        $data['id_venda'] = $id_pedido;
        $data['acao_venda'] = $action;
        

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

