<?php

namespace App\Model;

use Core\Library\ModelMain;

class RecebimentoModel extends ModelMain
{
    protected $table = 'tb_recebimento';
    protected $primaryKey = "REC_ID";
    public $validationRules = [];
    public $status_venda = [
        'A' => 'Aberto',
        'B' => 'Baixado',
        'C' => 'Cancelado',
    ];

}