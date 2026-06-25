<?php 

namespace App\Model;
use Core\Library\ModelMain;

class ProdutoModel extends ModelMain
{
    protected $table = 'tb_produto';
    protected $primaryKey = "PRD_ID";

    public $validationRules = [
        "prd_descricao" => [
            "label" => "Descrição",
            "rules" => "required|min:3|max:45"
        ],
        "prd_status" => [
            "label" => "Status",
            "rules" => "required"
        ]
    ];

    public function filtroListagem(array $post)
    {
        extract($post);
        $sql = "select * from {$this->table}";
        $sqlparte = [];
        $params = [];
        if (!empty(trim($filtroNomeProduto))) {
            array_push($sqlparte, "prd_descricao like :nomeProduto");
            $params['nomeProduto'] = "%{$filtroNomeProduto}%";
        }
        if (!empty(trim($filtroCategoriaProduto))) {
            array_push($sqlparte, "prd_categoria = :categoriaProduto");
            $params['categoriaProduto'] = $filtroCategoriaProduto;
        }
        if (!empty(trim($filtroEstoqueProduto))) {

            if ($filtroEstoqueProduto == 'sem') {
                array_push($sqlparte, " prd_estoque = 0");
            }
            if ($filtroEstoqueProduto == 'disp') {
                array_push($sqlparte, " prd_estoque > prd_estoque_min");
            }
            if ($filtroEstoqueProduto == 'min') {
                array_push($sqlparte, " prd_estoque <= prd_estoque_min or prd_estoque = 0");
            }
        }

        $sql .= (count($sqlparte) > 0) ? ' where ' . implode(' and ', $sqlparte) : '';
        $pdo = $this->db->dbSelect($sql, $params);
        return $this->db->dbBuscaArrayAll($pdo);
    }

    public function getProdutosIds($ids)
    { 
        $pdo = $this->db->dbSelect(
            "SELECT PRD_DESCRICAO FROM {$this->table} WHERE PRD_ID  in (:PRD_ID)",
            [
                ':PRD_ID' => $ids
            ]
        );
        return $this->db->dbBuscaArrayAll($pdo);
    }

    public function tem_estoque($id_produto, $qtd_produto)
    {
        $pdo = $this->db->dbSelect(
            "SELECT PRD_ID FROM {$this->table} WHERE PRD_ID = :PRD_ID AND PRD_ESTOQUE >= :PRD_QTD", 
            [
                ':PRD_ID' => $id_produto,
                ':PRD_QTD' => $qtd_produto
            ]
        );
        return isset($this->db->dbBuscaArray($pdo)['PRD_ID']);
    }

    public function listagem_produtos($ordem) {
        $pdo = $this->db->dbSelect(
            "SELECT * FROM {$this->table} order by :ORDEM",
            [':ORDEM' => $ordem]
        );
        return $this->db->dbBuscaArrayAll($pdo);
    }
}
