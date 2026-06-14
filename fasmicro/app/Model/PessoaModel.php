<?php

namespace App\Model;

use Core\Library\ModelMain;

class PessoaModel extends ModelMain
{
    protected $table = 'tb_pessoa';
    protected $primaryKey = "PES_ID";
    public $validationRules = [];
}