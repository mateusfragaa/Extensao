<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use App\Model\ProdutoModel;
use App\Model\VendaItemModel;

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

    /*
    * Prepara o formulário para as ações e faz o require do mesmo
    */
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
            default:
                $data["action_form"] = "view";
                break;
        }

        if ($acao != 'cadastro') $data["vendas"] = $this->model->getById($id);
            
        // Carrega os produtos para a pesquisa de itens para a venda
        $produtoModel = new ProdutoModel();
        $data['produtos'] = $produtoModel->lista('prd_descricao');

        $this->view(
            'admin/form/formVenda',
            [
                "data" => $data
            ],
            'sistema'
        );
    }

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

    public function inicioVenda()
    {   
        /*
         1 - criar o pedido
         2 - com o id do pedido criado adicionar itens
         3 - so posso criar pedido no inicio depois e atualizar o existente
        */
        $resultado = [];

        foreach ($_POST['produto'] as $produtoId => $dados) {

            if (!isset($dados['selecionado'])) {
                continue;
            }

            $resultado[] = [
                'prd_id' => (int) $produtoId,
                'qtd' => (int) $dados['qtd']
            ];
        }
        
        $id_pedido = $this->model->criarPedido();
        $venda_item = $this->loadModel('VendaItem');
        $venda_item->addProdutoPedido($id_pedido, $resultado);
        var_dump($venda_item->select_produto_venda($id_pedido));
        Redirect::page('admin/listaVenda');
    }

    public function editandoVenda($idVenda)
    {
        // preciso do id do pedido
        // colocar os dados dos produtos no pedido
        // atualiazar a tela com js
    }

    
}

