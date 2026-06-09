<?php

namespace App\Model;

use Core\Library\ModelMain;
use Exception;
use Override;

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

    public function criarPedido()
    {
        $id =  $this->db->dbInsert('insert into tb_pedido_venda(pev_cliente_id)values(:cliente)',['cliente' => 1]);
        return $id;
    }

    public function getVenda($id)
    {
        $pdo = $this->db->dbSelect('select * from tb_pedido_venda where pev_id = :venda', [':venda' => $id]);
        return $this->db->dbBuscaArray($pdo);;
    }

    public function updateValorTotal($acrescimo, $desconto, $venda)
    {
        $this->db
        // Trocar para fazer a query manualmente
        ->dbUpdate("update {$this->table} set PEV_ACRESCIMO = :acrescimo, PEV_DESCONTO = :desconto where {$this->primaryKey} = :venda", [
            ':acrescimo' => number_format($acrescimo, 2, '.', ','),
            ':desconto' => number_format($desconto, 2, '.', ','),
            ':venda' => $venda
        ]);
        $pdo = $this->db->dbSelect('select pev_total from tb_pedido_venda where pev_id = :venda', [':venda' => $venda]);
        return $this->db->dbBuscaArray($pdo);
    }
}