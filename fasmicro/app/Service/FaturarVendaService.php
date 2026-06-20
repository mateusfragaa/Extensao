<?php
namespace App\Service;

use App\Model\TipoDocumentoModel;
use App\Model\VendaModel;

class FaturarVendaService{
    private $vendaModel;
    private $tipoDocumentoModel;
    private $recebimentoModel;

    public function __construct() {
        $this->vendaModel = new VendaModel();
        $this->tipoDocumentoModel = new TipoDocumentoModel();
        // $this->recebimentoModel = new RecebimentoModel();
    }
}