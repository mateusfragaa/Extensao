<?php 

namespace App\Model;
use Core\Library\ModelMain;
use Core\Library\Session;

class ProdutoModel extends ModelMain
{
    protected $table = 'tb_produto';

    public function find(mixed $id = null)
    {
        if ($id) {
            $produto = $this->db->where('prd_id', $id)->first();
            if (count($produto) == 0) {
                Session::set('msgErrorForm','Produto não existe');
                return false;
            }
            return $produto;
        } else {
            return $this->db->findAll(); 
        }
    }

    public function insert(array $post)
    {
        if ($this->valida_produto($post)) return false;
    
        return $this->db->insert([
            'prd_descricao' => $post['prd_nome'],
            'prd_categoria' => $post['prd_categoria'],
            'prd_preco_custo' => $post['prd_custo'],
            'prd_preco_venda' => $post['prd_venda'],
            'prd_estoque' => $post['prd_estoque'],
            'prd_estoque_min' => $post['prd_estoque_min'],
            'prd_status' => $post['prd_status'],
            'prd_unidade' => $post['prd_unidade'],
            'prd_observacao' => $post['prd_observacao'],
        ]);
    }

    public function update(array $post,int $id)
    {
        if ($this->valida_produto($post)) return false;

        return $this->db->where('prd_id',$id)->update([
            'prd_descricao' => $post['prd_nome'],
            'prd_categoria' => $post['prd_categoria'],
            'prd_preco_custo' => $post['prd_custo'],
            'prd_preco_venda' => $post['prd_venda'],
            'prd_estoque' => $post['prd_estoque'],
            'prd_estoque_min' => $post['prd_estoque_min'],
            'prd_status' => $post['prd_status'],
            'prd_unidade' => $post['prd_unidade'],
            'prd_observacao' => $post['prd_observacao'],
        ]);
    }

    public function delete(int $id) {
        if (!$this->find($id)) return false;
        return $this->db->where('prd_id', $id)->delete();
    }

    public function filtroListagem(array $post)
    {   
        extract($post);
        $sql = "select * from {$this->table}";
        $sqlparte = [];
        $params = [];
        if (!empty(trim($filtroNomeProduto))) {
           array_push($sqlparte,"prd_descricao like :nomeProduto");
           $params['nomeProduto'] = "%{$filtroNomeProduto}%";
        }
        if (!empty(trim($filtroCategoriaProduto))) {
            array_push($sqlparte, "prd_categoria = :categoriaProduto");
            $params['categoriaProduto'] = $filtroCategoriaProduto;
        }
        if (!empty(trim($filtroEstoqueProduto))) {
            
            if ($filtroEstoqueProduto == 'sem') {
                array_push($sqlparte," prd_estoque = 0");
            }
            if ($filtroEstoqueProduto == 'disp') {
                array_push($sqlparte," prd_estoque > prd_estoque_min");
            }
            if ($filtroEstoqueProduto == 'min') {
                array_push($sqlparte," prd_estoque <= prd_estoque_min and prd_estoque > 0");
            }
        }
    
        $sql .= (count($sqlparte) > 0) ? ' where ' .implode(' and ', $sqlparte) : '';
        var_dump($sql,$params);
        $pdo = $this->db->dbSelect($sql,$params);
        return $this->db->dbBuscaArrayAll($pdo);
        die();
    }

    public function valida_produto(array $dados)
    {   
        $campoErros = [];
        if (empty($dados['prd_nome']) || strlen($dados['prd_nome']) < 4 || !is_string($dados['prd_nome'])) {
           array_push($campoErros,"Nome") ;
        }

        if (!empty($dados['prd_categoria'])) {
            if (strlen($dados['prd_categoria']) < 4 || !is_string($dados['prd_categoria'])) {
                array_push($campoErros, "Categoria");
            }
        }

        if (!empty($dados['prd_custo'])) {
            if ($dados['prd_custo'] < 0 || !is_numeric($dados['prd_custo'])) {
                array_push($campoErros, "Preço de Custo");
            }
        }

        if (empty($dados['prd_venda']) || $dados['prd_venda'] < 0 || !is_numeric($dados['prd_venda'])) {
            array_push($campoErros, "Preço de Venda");
        }

        if (!empty($dados['prd_estoque'])) {
            if ($dados['prd_estoque'] < 0 || !is_numeric($dados['prd_estoque'])) {
                array_push($campoErros, "Estoque Atual");
            }
        }

        if (!empty($dados['prd_estoque_min'])) {
            if ($dados['prd_estoque_min'] < 0 || !is_numeric($dados['prd_estoque_min'])) {
                array_push($campoErros, "Estoque Atual Mínimo");
            }
        }

        if (empty($dados['prd_status']) || ($dados['prd_status'] != '0' && $dados['prd_status'] != '1')) {
                array_push($campoErros, "Status");
        }

        if (empty($dados['prd_unidade']) || strlen($dados['prd_unidade']) != 2 ) {
            array_push($campoErros, "Unidade");
        }

        if (strlen(trim($dados['prd_observacao'])) > 500)
        {
            array_push($campoErros, "Observação");
        }
        return count($campoErros) > 0 ?
            Session::set("msgErrorForm", "Dados inválidos para cadastro do produto, verifique os campos: " .implode(", ",$campoErros))
            : 
            false;
    }
}
