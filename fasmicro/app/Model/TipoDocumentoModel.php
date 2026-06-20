<?php

namespace App\Model;

use Core\Library\ModelMain;

class TipoDocumentoModel extends ModelMain
{
    protected $table = 'tb_tipo_documento';
    protected $primaryKey = "TDC_ID";
    public $validationRules = [];

}