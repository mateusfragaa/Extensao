<?php

namespace App\Controller;

use App\Service\PedidoVenda;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use Core\Library\Session;

class Venda extends ControllerMain
{
    public function __construct()
    {   
        $this->loadHelper(['formHelper','vendaHelper']);
        return parent::__construct();
    }

    public function index(bool $temFiltro)
    {
        if (!$temFiltro) {
            $this->view(
                'admin/listaVenda',
                [
                    "vendas" => $this->model->lista('pev_status'),
                ],
                'sistema'
            );
        }else {
            $this->view(
                'admin/listaVenda',
                [
                    "vendas" => $this->model->filtroListagem($_POST)
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
    // Prepara o formulário da esquerda do formVenda.php
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

        if ($acao == 'insert') {
            $produtoModel = $this->loadModel('Produto');
            $data['produtos'] = $produtoModel->lista('prd_descricao');
            unset($produtoModel);
        }else{
            $data["vendas"] = $this->model->getById($id);
        }
        // Carrega os produtos para a pesquisa de itens para a venda

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
    public function update()
    {
        if ($this->model->update($_POST)) {
            Redirect::page("venda/", ['msgSucesso' => 'Sucesso ao atualizar o registro']);
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

    public function inicioVenda()
    {   
        $data = [];
        $serviceVenda = new PedidoVenda();
        $data['produtos_pedidos'] = $serviceVenda->comecarPedidoVenda($_POST);
        $data['produtos'] = $serviceVenda->listaProduto('prd_descricao');
        $data['info_venda'] = $serviceVenda->getVenda(Session::getDestroy('id_pedido_editando'));
        $data['action_form_modal'] = 'editandoVenda';
        $data['action_form'] = 'editandoVenda';

        $this->view(
            'admin/form/formVenda',
            [
                "data" => $data
            ],
            'sistema'
        );   
    }

    public function calculaTotalVenda() {
        $data = json_decode(file_get_contents('php://input'), true);
        $serviceVenda = new PedidoVenda();
        $novoTotal = $serviceVenda->calcularTotal($data['acrescimo'], $data['desconto'], $data['venda']);
        echo json_encode($novoTotal);
    }

    public function editandoVenda($action = '', $id_pedido = 0)
    {
        echo('Ação - '.$action);
        echo('<br>');
        echo('ID - '.$id_pedido);
        var_dump('post', $_POST);

        /**
         * 1- verificar se é editando a venda ou os itens atráves da ação ✅
         * 2- chamar o service com os dados novos e atualizar a venda ✅
         * 3- retornar os dados e itens da vendas com os produtos para selecionar para incluir mais produtos
         * 4- tentar o redirect caso não de certo usar  a view
         */

        $data = [];
        $serviceVenda = new PedidoVenda();
        
        if ($action == 'modal') {
            $serviceVenda->addProdutoPedido($id_pedido, $_POST);
            $data['produtos_pedidos'] = $serviceVenda->select_produto_venda($id_pedido);
            $data['produtos'] = $serviceVenda->listaProduto('prd_descricao');
            $data['info_venda'] = $serviceVenda->getVenda($id_pedido);
            $data['action_form_modal'] = 'editandoVenda';
            $data['action_form'] = 'editandoVenda';
        }else {
            $this->update([
                'PEV_STATUS' => $_POST[''],
                'PEV_ACRESCIMO' => $_POST[''],
                'PEV_DESCONTO' => $_POST[''],
                'PEV_CLIENTE_ID' => $_POST[''],
                'PEV_SUB_TOTAL' => $_POST[''],
                'PEV_TOTAL' => $_POST['']
            ]);
        }

        $this->view(
            'admin/form/formVenda',
            [
                "data" => $data
            ],
            'sistema'
        );
    }

    
}

