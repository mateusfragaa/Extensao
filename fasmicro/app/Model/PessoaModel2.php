<?php

namespace App\Model;

use Core\Library\ModelMain;

class PessoaModel2 extends ModelMain
{
    protected $table = "tb_pessoa";

    public $primaryKey = 'PES_ID';

    public function getPessoas()
    {
        return $this->db->findAll();
    }
}