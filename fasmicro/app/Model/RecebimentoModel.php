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

    public function gravar_recebimento($forma_pagamento,$quantidade,$valor, $id_pedido)
    {
        $pdo = $this->db->dbSelect('call sp_gravar_recebimento(:forma,:quantidade,:valor,:id_pedido)',
        [
            ':forma' => $forma_pagamento,
            ':quantidade' => $quantidade,
            ':valor' => $valor
        ]);

        $mensagem = $this->db->dbBuscaArrayAll($pdo);
        var_dump($mensagem);
        die();
    }
}