<?php

namespace App\Service;

use App\Model\TipoDocumentoModel;
use App\Model\RecebimentoModel;
use App\Model\VendaModel;
use Core\Library\Redirect;
use Core\Library\Session;

class RecebimentoService
{
    private $vendaModel;
    private $tipoDocumentoModel;
    private $recebimentoModel;

    public function __construct()
    {
        $this->tipoDocumentoModel = new TipoDocumentoModel();
        $this->recebimentoModel = new RecebimentoModel();
        $this->vendaModel = new VendaModel();
    }

    
}
