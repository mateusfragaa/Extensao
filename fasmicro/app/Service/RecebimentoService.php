<?php

namespace App\Service;

use App\Model\TipoDocumentoModel;
use App\Model\RecebimentoModel;
use App\Model\VendaModel;
use App\Model\PessoaModel;
use Core\Library\Redirect;
use Core\Library\Session;

class RecebimentoService
{
    private $vendaModel;
    private $tipoDocumentoModel;
    private $recebimentoModel;
    private $pessoaModel;

    public function __construct()
    {
        $this->tipoDocumentoModel = new TipoDocumentoModel();
        $this->recebimentoModel = new RecebimentoModel();
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

    public function update(array $dados)
    {
        return $this->recebimentoModel->update_recebimento($dados);
    }

    public function delete($id)
    {
        return $this->recebimentoModel->apagar_recebimento([$id]);
    }
    

}
