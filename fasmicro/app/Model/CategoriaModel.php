<?php

namespace App\Model;

use Core\Library\ModelMain;

class CategoriaModel extends ModelMain
{
    protected $table = "categoria";

    public $listaStatus = [
        1 => "Ativo",
        2 => "Inativo"
    ];

}