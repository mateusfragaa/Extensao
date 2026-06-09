<?php

namespace App\Model;

use Core\Library\ModelMain;

class VendaItemModel extends ModelMain
{
    protected $table = 'tb_pedido_venda_item';
    protected $primaryKey = "PEVI_ID";
    public $validationRules = [];

    public function addProdutoPedido($id_pedido, $produtos)
    {
        // $this->db->beginTransaction();
        try {
            foreach ($produtos as $key => $value) {
                $this->db->insert([
                    'pevi_venda_id' => $id_pedido,
                    'pevi_prd_id' => $value['prd_id'],
                    'pevi_quantidade' => $value['qtd'],
                    'pevi_preco_unitario' => $value['valorVenda']
                ]);
            }
                    
                    // $this->db->commit()
            return 'true';
        } catch (\PDOException $e) {

            // $this->db->rollBack();

            die($e->getMessage());
        }
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
}