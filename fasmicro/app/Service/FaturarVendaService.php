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
        if ($recebimento['forma_pagamento'] <= 0 || $recebimento['quantidade'] <= 0 || $recebimento['valor'] <= 0) {
            Session::set('msgError', 'Verifique dados da forma de pagamento, quantidade e valor, pois estão inválidos para cadastro');
            Redirect::page("faturarVenda/formFaturar/receber/$id_pedido");
            exit;
        }elseif (!in_array($recebimento['forma_pagamento'], $this->tipoDocumentoModel->getParcela())) {
            Session::set('msgError', 'Não é permitido ter parcelamento com essa forma de pagamento');
            Redirect::page("faturarVenda/formFaturar/receber/$id_pedido");
            exit;
        }
        
        $this->recebimentoModel->gravar_recebimento($recebimento['forma_pagamento'], $recebimento['quantidade'], $recebimento['valor'],$id_pedido);
    }
}