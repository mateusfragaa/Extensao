<?php
namespace App\Service;

use App\Model\PessoaModel;
use App\Model\VendaModel;
use App\Model\VendaItemModel;
use App\Model\ProdutoModel;
use Core\Library\Session;

class PedidoVenda 
{
    private $vendaModel;
    private $vendaItemModel;
    private $produtoModel;
    private $pessoaModel;

    public function __construct()
    {
        $this->vendaModel = new VendaModel();
        $this->vendaItemModel = new VendaItemModel();
        $this->produtoModel = new ProdutoModel();
        $this->pessoaModel = new PessoaModel();
    }

    public function comecarPedidoVenda($post)
    {
        // Vai somente criar e adicionar os itens iniciais do pedido e retornar os itens
        $id_pedido_criado = $this->vendaModel->criarPedido();
        Session::set('id_pedido_editando', $id_pedido_criado);
        
        $this->addProdutoPedido($id_pedido_criado, $post);
        return $this->select_produto_venda($id_pedido_criado);
    }

    public function calcularTotal($acrescimo, $desconto, $venda)
    {
        // Lógica de validação
        // $pedido_venda = $this->vendaModel->getVenda($venda);
        // Desconto não pode maior que o valor total da venda
        // Não pode dar acrescimo se o valor da venda estiver zerado
        if (empty($acrescimo)) $acrescimo = 0;
        if (empty($desconto)) $desconto = 0;
        if (empty($venda)) $venda = 0;
        return $this->vendaModel->updateValorTotal($acrescimo, $desconto, $venda);
    }

    public function getStatusVenda()
    {
        return $this->vendaModel->getStatus();
    }

    public function getVenda($id)
    {
        return $this->vendaModel->getVenda($id);
    }

    public function updateVenda($post,$id_pedido)
    {
        $post = [
            'PEV_ID' => $id_pedido,
            'pev_data_venda' => $post['data_venda'],
            'pev_cliente_id' => $post['cliente_venda'],
            'pev_status' => $post['status_venda']
        ];
    
        return $this->vendaModel->update($post);
    }

    public function apagarVendaEItens($id_pedido)
    {
        $this->vendaModel->delete(['PEV_ID' => $id_pedido]);
        // $this->vendaItemModel->apagarItensVenda($id_pedido);
    }

    public function select_produto_venda($id)
    {
        return $this->vendaItemModel->select_produto_venda($id);
    }

    public function excluirProduto($id_produtos)
    {
        // Lógica para apagar os produtos com base na sequencia venda item
        return $this->vendaItemModel->apagarProdutoPedido($id_produtos);
    }

    public function listaProduto($ordem)
    {
        return $this->produtoModel->lista($ordem);
    }

    public function addProdutoPedido($id, $post)
    {
        // Vai receber o id do pedido e os produtos e gravar no banco de dados
        $resultado = [];
        foreach ($post['produto'] as $produtoId => $dados) {
            if (!isset($dados['selecionado'])) {
                continue;
            }
            $resultado[] = [
                'prd_id' => (int) $produtoId,
                'qtd' => (int) $dados['qtd'],
                'valorVenda' => (float) $dados['valorVenda']
            ];

            $this->vendaItemModel->addProdutoPedido($id, $resultado);
        }
    }

    public function listaPessoa($ordem)
    {
        return $this->pessoaModel->lista($ordem);
    }
}

/**
 * 'cliente_venda' => string 'Selecione o Cliente' (length=19)
  'data_venda' => string '2026-06-19' (length=10)
  'status_venda' => string 'C' (length=1)
  'acrescimo_venda' => string '3' (length=1)
  'desconto_venda' => string '2' (length=1)
  'venda_sub_total' => string '' (length=0)
  'venda_id' => string '336' (length=3)
 */
