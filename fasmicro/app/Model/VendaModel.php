<?php

namespace App\Model;

use Core\Library\ModelMain;

class VendaModel extends ModelMain
{
    protected $table = 'tb_pedido_venda';
    protected $primaryKey = "PEV_ID";
    public $validationRules = [];

    public function filtroListagem($dados)
    {
        extract($dados);
        $sql = "select * from {$this->table}";
        $sqlparte = [];
        $params = [];

        // filtro por id ou nome
        if (!empty(trim($id_nome_cliente))) {

            if (is_numeric($id_nome_cliente)) {
                $sqlparte[] = "pev_id = :pev_id";
                $params[':pev_id'] = $id_nome_cliente;
            } else {

                $sqlparte[] = "pev_cliente_nome like :pev_cliente_nome";
                $params[':pev_cliente_nome'] = "%{$id_nome_cliente}%";
            }
        }

        // filtro status
        if (!empty(trim($status_venda))) {
            $sqlparte[] = "pev_status = :status_venda";
            $params[':status_venda'] = $status_venda;
        }

        // filtro data
        if (!empty(trim($data_inicio)) && !empty(trim($data_fim))) {
            $sqlparte[] = "pev_data_venda between :data_inicio and :data_fim";
            $params[':data_inicio'] = $data_inicio;
            $params[':data_fim'] = $data_fim;
        }

        $sql .= count($sqlparte) > 0 ? ' where ' . implode(' and ', $sqlparte) : '';
        $pdo = $this->db->dbSelect($sql, $params);
        return $this->db->dbBuscaArrayAll($pdo);
    }
}
