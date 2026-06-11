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
            $produtoModel = $this->loadModel('Produto');
            $data['produtos'] = $produtoModel->lista('prd_descricao');
            unset($produtoModel);
        } else {
            // die('carrega a venda id');
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
    public function update()
    {
        if ($this->serviceVenda->updateVenda($_POST)) {
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
    // Formulário da direita
    public function inicioVenda()
    {   
        $data = [];
        $data['produtos_pedidos'] = $this->serviceVenda->comecarPedidoVenda($_POST);
        $data['produtos'] = $this->serviceVenda->listaProduto('prd_descricao');
        $data['info_venda'] = $this->serviceVenda->getVenda(Session::getDestroy('id_pedido_editando'));
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

    public function calculaTotalVenda() {
        $data = json_decode(file_get_contents('php://input'), true);
        $novoTotal = $this->serviceVenda->calcularTotal($data['acrescimo'], $data['desconto'], $data['venda']);
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
        $this->serviceVenda->addProdutoPedido($id_pedido, $_POST);
        $data['produtos_pedidos'] = $this->serviceVenda->select_produto_venda($id_pedido);
        $data['produtos'] = $this->serviceVenda->listaProduto('prd_descricao');
        $data['info_venda'] = $this->serviceVenda->getVenda($id_pedido);
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
}

