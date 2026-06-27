<?php

namespace App\Model;

use Core\Library\ModelMain;
use Core\Library\Session;

class VendaItemModel extends ModelMain
{
    protected $table = 'tb_pedido_venda_item';
    protected $primaryKey = "PEVI_ID";
    public $validationRules = [];

    public function addProdutoPedido($id_pedido, $produto, $quantidade)
    {
        
        $pdo = $this->db->dbSelect(
            'call sp_add_produto_pedido(:id_pedido, :produto, :quantidade)',
            [
                ':id_pedido' => $id_pedido,
                ':quantidade' => $quantidade,
                ':produto' => $produto
            ]
        );

        return  $this->db->dbBuscaArray($pdo);
    }

    public function select_produto_venda($id_pedido)
    {
        return $this->db
            ->select(
                "p.prd_id,
                    p.prd_descricao,
                    tb_pedido_venda_item.*"
            )->join("tb_produto p", "p.prd_id = PEVI_PRD_ID", "inner")
            ->where("pevi_venda_id", $id_pedido)
            ->findAll();
    }

    public function apagarProdutoPedido($ids)
    {
        return $this->db->whereIn('pevi_id', $ids)->delete();
    }

    public function apagarItensVenda($id)
    {
        return $this->db->where('pevi_venda_id', $id)->delete();
    }
}
