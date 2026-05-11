<?php

namespace App\Model;

use Core\Library\ModelMain;

class VendaModel extends ModelMain
{
    protected $table = 'tb_pedido_venda';
    protected $primaryKey = "PEV_ID";
    public $validationRules = [];
}
