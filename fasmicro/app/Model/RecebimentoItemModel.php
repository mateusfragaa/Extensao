<?php

namespace App\Model;

use Core\Library\ModelMain;
use Core\Library\Session;

class RecebimentoItemModel extends ModelMain
{
    protected $table = 'tb_recebimento_item';
    protected $primaryKey = "reci_id";
    public $validationRules = [];

    public function apagar_recebimento_item($ids)
    {
        // Converte o array do PHP ["5", "6"] em uma string JSON -> '["5", "6"]'
        $jsonIds = json_encode(array_values($ids));

        $pdo = $this->db->dbSelect(
            "CALL sp_estornar_recebimento_item(:json_ids)",
            [
                ':json_ids' => $jsonIds,
            ]
        );

        $retorno = $this->db->dbBuscaArray($pdo);

        if (!$retorno['sucesso']) {
            Session::set('msgError', $retorno['mensagem']);
            return false;
        }

        Session::set('msgSucesso', $retorno['mensagem']);
        return true;
    }

    public function buscar_itens_por_data()
    {
        // A query filtra pela data de criação do item (assumindo que reci_created_at 
        // ou similar exista na tabela de itens) e ignora pais com status 'C'
        $sql = "
        SELECT 
            RI.RECI_ID,
            RI.RECI_REC_ID,
            TD.TDC_DESCRICAO,
            P.PES_NOME,
            RI.RECI_VALOR,
            RI.RECI_DATA
        FROM 
            tb_recebimento_item RI
        INNER JOIN 
            tb_recebimento R ON RI.RECI_REC_ID = R.REC_ID
        INNER JOIN 
            tb_pessoa P ON R.REC_DEVEDOR_ID = P.PES_ID
        INNER JOIN 
            tb_tipo_documento TD ON RI.RECI_TIPO_DOCUMENTO = TD.TDC_ID
        WHERE 
            DATE(RI.RECI_CREATED_AT) = :data
            AND R.REC_STATUS <> 'C'
        ORDER BY 
            R.REC_ID DESC
    ";
        $pdo = $this->db->dbSelect($sql, [':data' => date('Y-m-d')]);
        return $this->db->dbBuscaArrayAll($pdo);
    }

   
}
