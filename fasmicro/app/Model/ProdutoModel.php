<?php 

namespace App\Model;
use Core\Library\ModelMain;

class ProdutoModel extends ModelMain
{
    protected $table = 'tb_produto';
    protected $primaryKey = "PRD_ID";

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
                array_push($sqlparte, " prd_estoque <= prd_estoque_min and prd_estoque > 0");
            }
        }

        $sql .= (count($sqlparte) > 0) ? ' where ' . implode(' and ', $sqlparte) : '';
        $pdo = $this->db->dbSelect($sql, $params);
        return $this->db->dbBuscaArrayAll($pdo);
    }
}
