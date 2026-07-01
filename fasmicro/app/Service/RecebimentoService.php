<?php

namespace App\Service;

use App\Model\TipoDocumentoModel;
use App\Model\RecebimentoModel;
use App\Model\VendaModel;
use App\Model\PessoaModel;
use App\Model\RecebimentoItemModel;
use Core\Library\Redirect;
use Core\Library\Session;

class RecebimentoService
{
    private $vendaModel;
    private $tipoDocumentoModel;
    private $recebimentoModel;
    private $recebimentoItemModel;
    private $pessoaModel;

    public function __construct()
    {
        $this->tipoDocumentoModel = new TipoDocumentoModel();
        $this->recebimentoModel = new RecebimentoModel();
        $this->recebimentoItemModel = new RecebimentoItemModel();
        $this->vendaModel = new VendaModel();
        $this->pessoaModel = new PessoaModel();
    }

    public function lista_pessoa()
    {
        return $this->pessoaModel->lista('pes_nome');
    }

    public function lista_status()
    {
        return $this->recebimentoModel->getStatus();
    }

    public function lista_tipo_documento()
    {
        return $this->tipoDocumentoModel->lista('tdc_descricao');
    }

    public function lista_recebimentos()
    {
        return $this->recebimentoModel->buscar_recebimento_completo();
    }

    public function getMetricas()
    {
        return $this->recebimentoModel->buscar_metricas_recebimento();
    }

    public function lista_recebimentos_baixa()
    {
        return $this->recebimentoModel->buscar_recebimento_completo_baixa();
    }

    public function lista_recebimentos_itens()
    {
        return $this->recebimentoItemModel->buscar_itens_por_data();
    }

    public function update(array $dados)
    {
        return $this->recebimentoModel->update_recebimento($dados);
    }

    public function delete($id)
    {
        return $this->recebimentoModel->apagar_recebimento([$id]);
    }

    public function baixarRecebimentos(array $post)
    {
        if (empty($post['recebimentos_ids'])) {
            Session::set('msgError', 'Selecione ao menos um recebimento.');
            return false;
        }

        $valorPago = str_replace(',', '.', $post['valor_pago']);

        return $this->recebimentoModel->baixar_recebimento(
            $post['recebimentos_ids'],
            $valorPago,
            $post['forma_pagamento']
        );
    }

    public function apagar_recebimento_item($post)
    {
        if (!isset($post['recebimentos_ids'])) {
            Redirect::page("baixarRecebimento/formBaixar/", ['msgError' => 'Nenhum recebimento selecionado.']);
        }
        $ids = $post['recebimentos_ids'];
        $this->recebimentoItemModel->apagar_recebimento_item($ids);
    }

    public function receber_recebimento($post)
    {   
        if (!isset($post['recebimentos_ids'])) {
            Redirect::page("baixarRecebimento/formBaixar/", ['msgError' => 'Nenhum recebimento selecionado.']);
        }
        $ids = $post['recebimentos_ids'];
        $forma_pagamento = $post['forma_pagamento'];
        $valor_pago = $post['valor_pago'];
        $this->recebimentoModel->baixar_recebimento($ids, $forma_pagamento, $valor_pago);
    }
}
