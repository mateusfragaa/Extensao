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

    public function lista()
    {
        // $rs = $this->db->dbSelect("SELECT * FROM {$this->table} WHERE statusRegistro = 1");
        // return $this->db->dbBuscaArrayAll($rs);

        return $this->db
            ->orderBy("descricao")
            ->findAll();
    }
}