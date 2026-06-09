<?php
namespace App\Service;
use App\Model\VendaModel;
use App\Model\VendaItemModel;
use App\Model\ProdutoModel;
use Core\Library\Session;

class PedidoVenda 
{
    private $vendaModel;
    private $vendaItemModel;
    private $produtoModel;

    public function __construct() {
        $this->vendaModel = new VendaModel();
        $this->vendaItemModel = new VendaItemModel();
        $this->produtoModel = new ProdutoModel();
    }

    public function comecarPedidoVenda($post)
    {
        // Vai somente criar e adicionar os itens iniciais do pedido e retornar os itens
        $id_pedido_criado = $this->vendaModel->criarPedido();
        Session::set('id_pedido_editando', $id_pedido_criado);
        $this->addProdutoPedido($id_pedido_criado, $post);
        return $this->select_produto_venda($id_pedido_criado);
    }

    public function addProdutoPedido($id,$post)
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

            $this->vendaItemModel->addProdutoPedido($id,$resultado);
        }
    }

    public function calcularTotal($acrescimo, $desconto, $venda){
        // Lógica de validação
        // $pedido_venda = $this->vendaModel->getVenda($venda);
        // Desconto não pode maior que o valor total da venda
        // Não pode dar acrescimo se o valor da venda estiver zerado
        if (empty($acrescimo)) $acrescimo = 0;
        if (empty($desconto)) $desconto = 0;
        if (empty($venda)) $venda = 0;
        return $this->vendaModel->updateValorTotal($acrescimo, $desconto, $venda);
    }

    public function select_produto_venda($id){
       return $this->vendaItemModel->select_produto_venda($id);
    }

    public function listaProduto($ordem) {
        return $this->produtoModel->lista($ordem);
    }

    public function getVenda($id) {
        return $this->vendaModel->getVenda($id);
    }


}
