<?php

namespace App\Model;

use Core\Library\ModelMain;

class PessoaModel extends ModelMain
{
    protected $table = 'tb_pessoa';
    protected $primaryKey = "PES_ID";

    public $validationRules = [
        "PES_NOME" => [
            "label" => "Nome Completo / Razão Social",
            "rules" => "required|min:3|max:45"
        ],
        "CPF_CNPJ" => [
            "label" => "CPF / CNPJ",
            "rules" => "required|cpf_cnpj"
        ],
        "EMAIL" => [
            "label" => "E-mail",
            "rules" => "required|email|max:50"
        ],
        "TIPO_PESSOA" => [
            "label" => "Tipo de Pessoa",
            "rules" => "required"
        ]
    ];

    public function filtroListagem(array $post)
    {
        extract($post);
        $sql = "select * from {$this->table}";
        $sqlparte = [];
        $params = [];
        
        if (!empty(trim($filtroNomePessoa))) {
            array_push($sqlparte, "PES_NOME like :nomePessoa");
            $params['nomePessoa'] = "%{$filtroNomePessoa}%";
        }

        $sql .= (count($sqlparte) > 0) ? ' where ' . implode(' and ', $sqlparte) : '';

        // Adiciona ordenação
        $ordemPermitida = ['PES_NOME', 'CIDADE', 'UF'];
        $ordem = (isset($ordemPessoa) && in_array($ordemPessoa, $ordemPermitida)) ? $ordemPessoa : 'PES_NOME';
        $sql .= " order by {$ordem} asc";

        $pdo = $this->db->dbSelect($sql, $params);
        return $this->db->dbBuscaArrayAll($pdo);
    }
}