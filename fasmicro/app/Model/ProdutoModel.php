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
        if (empty($ids)) {
            return [];
        }

        $placeholders = [];
        $params = [];

        foreach ($ids as $key => $id) {
            $placeholder = ":PRD_ID_{$key}";
            $placeholders[] = $placeholder;
            $params[$placeholder] = $id;
        }

        $pdo = $this->db->dbSelect(
            "SELECT PRD_DESCRICAO FROM {$this->table} WHERE PRD_ID IN (" . implode(',', $placeholders) . ")",
            $params
        );
        return $this->db->dbBuscaArrayAll($pdo);
    }

    public function listagem_produtos($ordem) {
        $pdo = $this->db->dbSelect(
            "SELECT * FROM {$this->table} order by :ORDEM",
            [':ORDEM' => $ordem]
        );
        return $this->db->dbBuscaArrayAll($pdo);
    }

    
}
