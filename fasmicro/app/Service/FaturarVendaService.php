<?php
namespace App\Service;

use App\Model\TipoDocumentoModel;
use App\Model\RecebimentoModel;
use App\Model\VendaModel;
use Core\Library\Redirect;
use Core\Library\Session;

class FaturarVendaService{
    private $vendaModel;
    private $tipoDocumentoModel;
    private $recebimentoModel;

    public function __construct() {
        $this->vendaModel = new VendaModel();
        $this->tipoDocumentoModel = new TipoDocumentoModel();
        $this->recebimentoModel = new RecebimentoModel();
    }

    public function dadosVenda($id_pedido)
    {
        return $this->vendaModel->buscarDadosCompletosVenda($id_pedido);
    }

    public function listaDocumento()
    {
        return $this->tipoDocumentoModel->lista('TDC_DESCRICAO');
    }

    public function gravarRecebimento($recebimento,$id_pedido)
    {   
        if (
            $recebimento['forma_pagamento'] <= 0 ||
            $recebimento['quantidade'] <= 0 || !is_numeric($recebimento['quantidade']) ||
            $recebimento['valor'] <= 0 || !is_numeric($recebimento['valor'])
            ) {
            Session::set('msgError', 'Verifique dados da forma de pagamento, quantidade e valor, pois estão inválidos para cadastro');
            Redirect::page("faturarVenda/formFaturar/receber/$id_pedido");
            exit;
        }
        elseif (!in_array($recebimento['forma_pagamento'], $this->tipoDocumentoModel->getParcela()) && $recebimento['quantidade'] > 1) {
            Session::set('msgError', 'Não é permitido ter parcelamento com essa forma de pagamento');
            Redirect::page("faturarVenda/formFaturar/receber/$id_pedido");
            exit;
        }
        
        
        $this->recebimentoModel->gravar_recebimento($recebimento['forma_pagamento'], $recebimento['quantidade'], $recebimento['valor'], $id_pedido);
        return $this->busca_recebimento_venda($id_pedido);
    }

    public function busca_recebimento_venda($id_pedido){
        return $this->recebimentoModel->buscar_recebimento($id_pedido);
    }

    public function excluir_recebimento($post,$id_pedido)
    {
        if($this->recebimentoModel->apagar_recebimento($post['recebimentos_selecionados'])){
            Session::set('msgSucesso', 'Recebimento removido com sucesso!');
            Redirect::page("faturarVenda/formFaturar/receber/$id_pedido");
            exit;
        }
        Session::set('msgError', 'Erro interno ao remover recebimento.');
        Redirect::page("faturarVenda/formFaturar/receber/$id_pedido");
        exit;
    }

    public function finalizar_venda($id_pedido)
    {
        $mensagem = $this->vendaModel->finalizar_venda($id_pedido);
        var_dump($mensagem); die();
        if (!$mensagem[0]['sucesso']) {
            Session::set('msgError', $mensagem[0]['mensagem']);
            Redirect::page("faturarVenda/formFaturar/receber/$id_pedido");
            exit;
        }

        Session::set('msgSucesso', $mensagem[0]['mensagem']);
        Redirect::page("venda/formVenda/insert/");
        exit;
    }
}