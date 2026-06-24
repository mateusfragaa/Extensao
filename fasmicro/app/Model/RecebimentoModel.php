<?php

namespace App\Model;

use Core\Library\ModelMain;
use Core\Library\Session;

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
        $pdo = $this->db->dbSelect('call sp_gravar_recebimento(:forma_pagamento,:quantidade_parcela,:valor_total,:id_pedido)',
        [
            ':forma_pagamento' => $forma_pagamento,
            ':quantidade_parcela' => $quantidade,
            ':valor_total' => $valor,
            ':id_pedido' => $id_pedido
        ]);

        $mensagem = $this->db->dbBuscaArrayAll($pdo);

        if ($mensagem[0]['sucesso']) {
            Session::set('msgSucesso', $mensagem[0]['mensagem']);
            return;
        }

        Session::set('msgError', $mensagem[0]['mensagem']);
    }

    public function buscar_recebimento($id_pedido)
    {
        $pdo = $this->db->dbSelect(
            "select * from $this->table where rec_venda_id = :id_pedido order by rec_id desc",
            [
                ':id_pedido' => $id_pedido
            ]
        );

        return $this->db->dbBuscaArrayAll($pdo);
    }

    public function apagar_recebimento($ids)
    {
        return $this->db->table('tb_recebimento')->whereIn('rec_id', $ids)->delete();
    }

}