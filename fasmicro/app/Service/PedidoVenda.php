<?php

namespace App\Service;

use App\Model\PessoaModel;
use App\Model\VendaModel;
use App\Model\VendaItemModel;
use App\Model\ProdutoModel;
use Core\Library\Redirect;
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

    public function criarPedido()
    {
        return $this->vendaModel->criarPedido();
    }

    public function comecarPedidoVenda($post)
    {
        $resultado = $this->validaProdutosSelecionados($post);

        if (count($resultado) > 0) {
            $id_pedido_criado = Session::get('id_pedido_editando');

            if (
                !$id_pedido_criado ||
                empty($this->vendaModel->getVenda($id_pedido_criado))
            ) {

                $id_pedido_criado = $this->criarPedido();

                Session::set(
                    'id_pedido_editando',
                    $id_pedido_criado
                );
            }

            $this->addProdutoPedido($id_pedido_criado, $resultado);
            return $id_pedido_criado;
        }
        return [];
    }

    public function calcularTotal($acrescimo, $desconto, $venda)
    {
        $venda = $this->getVenda($venda);
        if (isset($venda['PEV_STATUS']) && in_array($venda['PEV_STATUS'], ['F', 'C'])) {
            Session::set('msgError', 'Não e possível aplicar desconto ou acréscimo em pedido faturado');
            Redirect::page("faturarVenda/formFaturar/receber/".$venda['PEV_ID']);
            exit;
        }
        // Lógica de validação
        $pedido_venda = $this->vendaModel->getVenda($venda);
        // Desconto não pode maior que o valor total da venda
        if ($desconto > $pedido_venda['PEV_TOTAL']) {
            Session::set('msgError', 'O Desconto não pode ser maior que o valor da venda.');
            return $pedido_venda['PEV_TOTAL'];
        }
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

    public function updateVenda($post, $id_pedido)
    {
        $venda = $this->getVenda($id_pedido);
        if (isset($venda['PEV_STATUS']) && in_array($venda['PEV_STATUS'], ['F', 'C'])) {
            return false;
        }

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
        $venda = $this->getVenda($id_pedido);

        if (isset($venda['PEV_STATUS']) && in_array($venda['PEV_STATUS'], ['F', 'C'])) {
            return false;
        }

        return $this->vendaModel->deletar_venda($id_pedido);
    }

    public function select_produto_venda($id)
    {
        return $this->vendaItemModel->select_produto_venda($id);
    }

    public function excluirProduto($id_produtos, $id_pedido)
    {
        $venda = $this->getVenda($id_pedido);
        if (isset($venda['PEV_STATUS']) && in_array($venda['PEV_STATUS'], ['F', 'C'])) {
            return false;
        }
        return $this->vendaItemModel->apagarProdutoPedido($id_produtos);
    }

    public function listaProduto($ordem)
    {
        return $this->produtoModel->listagem_produtos($ordem);
    }

    public function addProdutoPedido($id_pedido, $post_produtos)
    {

        $venda = $this->getVenda($id_pedido);
        if (isset($venda['PEV_STATUS']) && in_array($venda['PEV_STATUS'], ['F', 'C'])) {
            return false;
        }

        foreach ($post_produtos as $key => $value) {
            $mensagem = $this->vendaItemModel->addProdutoPedido($id_pedido, $value['prd_id'], $value['qtd']);
            if (!$mensagem['sucesso']) {
                Redirect::page("venda/formVenda/update/$id_pedido", [$mensagem['mensagem']]);
                exit;
            }
        }
    }

    public function validaProdutosSelecionados($post_produtos)
    {
        $resultado = [];
        

        if (!isset($post_produtos['produto']) || !is_array($post_produtos['produto'])) {
            return $resultado;
        }

        foreach ($post_produtos['produto'] as $produtoId => $dados) {
            if (!isset($dados['selecionado']) || intval($dados['qtd']) <= 0) {
                continue;
            }

            $resultado[] = [
                'prd_id' => (int) $produtoId,
                'qtd' => (int) $dados['qtd'],
                'valorVenda' => (float) $dados['valorVenda']
            ];
        }

        return $resultado;
    }

    public function listaPessoa($ordem)
    {
        return $this->pessoaModel->lista($ordem);
    }

    public function cancelar_venda($id_pedido)
    {
        $mensagem = $this->vendaModel->cancelar_venda($id_pedido);
        if (!$mensagem[0]['sucesso']) {
            Session::set('msgError', $mensagem[0]['mensagem']);
            Redirect::page("Venda/");
            exit;
        }

        Session::set('msgSucesso', $mensagem[0]['mensagem']);
        Redirect::page("Venda/");
        exit;
    }
}
