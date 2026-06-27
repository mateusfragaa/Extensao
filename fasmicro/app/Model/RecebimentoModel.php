<?php

namespace App\Model;

use Core\Library\ModelMain;
use Core\Library\Session;

class RecebimentoModel extends ModelMain
{
    protected $table = 'tb_recebimento';
    protected $primaryKey = "REC_ID";
    public $validationRules = [];
    public $status_recebimento = [
        'A' => 'Aberto',
        'B' => 'Baixado',
        'PB' => 'Parcialmente baixado',
        'C' => 'Cancelado',
    ];

    public function getStatus()
    {
        return $this->status_recebimento;
    }

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

    public function buscar_recebimento_completo()
    {
        $pdo = $this->db->dbSelect("
                SELECT 
                    REC_ID,
                    REC_VALOR,
                    REC_CREATED_AT,
                    REC_STATUS,
                    REC_VALOR_PAGO,
                    REC_VALOR_ABERTO,
                    REC_OBSERVACAO,
                    REC_DATA_BAIXA,
                    REC_VENCIMENTO,
                    P.PES_NOME,
                    TD.TDC_DESCRICAO
                FROM
                    tb_recebimento R
                inner join tb_pedido_venda V
                    on r.REC_VENDA_ID = v.PEV_ID
                INNER JOIN tb_pessoa P
                    ON V.PEV_CLIENTE_ID = P.PES_ID
                INNER JOIN tb_tipo_documento TD
                    ON R.REC_TIPO_DOCUMENTO_ID = TD.TDC_ID
            ", []);

        return $this->db->dbBuscaArrayAll($pdo);
    }
}