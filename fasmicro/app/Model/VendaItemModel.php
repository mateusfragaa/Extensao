<?php

namespace App\Model;

use Core\Library\ModelMain;
use Exception;

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
                    'pevi_preco_unitario' => 40,
                ]);
            }

            // $this->db->commit();

            return 'true';
        } catch (\PDOException $e) {

            // $this->db->rollBack();

            die($e->getMessage());
        }
    }

"select
   p.prd_id,
   p.prd_descricao,
   vi.pevi_preco_unitario,
   vi.pevi_subtotal
from tb_pedido_venda_item vi
inner join tb_produto p
    on p.prd_id = vi.pevi_prd_id
where 
    vi.pevi_venda_id = :id_pedido"
}