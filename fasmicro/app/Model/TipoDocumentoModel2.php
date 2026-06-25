<?php

namespace App\Model;

use Core\Library\ModelMain;

class TipoDocumentoModel2 extends ModelMain
{
    protected $table = "tb_tipo_documento";

    public $primaryKey = 'TDC_ID';

    public $listaStatus = [
        '1' => 'ATIVO',
        '2' => 'INATIVO',
    ];
}