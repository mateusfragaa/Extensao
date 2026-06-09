<?php

namespace App\Model;

use Core\Library\ModelMain;
use Core\Library\DatabasePessoa;
use Core\Library\Validator;
use Core\Library\Session;

class PessoaModel extends ModelMain
{
    protected $table      = 'tb_pessoa';
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

    /**
     * Troca $this->db por DatabasePessoa que tem os wrappers
     * com tratamento de erro amigável.
     * Database.php original permanece intocado.
     */
    public function __construct()
    {
        parent::__construct();

        $this->db = new DatabasePessoa(
            $_ENV['DB_CONNECTION'],
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_DATABASE'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );

        $this->db->table($this->table);
    }

    /**
     * Sobrescreve insert usando o wrapper com tratamento amigável.
     */
    public function insert($dados)
    {
        if (Validator::make($dados, $this->validationRules)) {
            return false;
        }

        try {
            unset($dados[$this->primaryKey]);
            $idGerado = $this->db->insertComTratamento($dados);
            return $idGerado > 0 ? $idGerado : false;
        } catch (\Exception $e) {
            $this->handleDatabaseError($e);
            return false;
        }
    }

    /**
     * Sobrescreve update usando o wrapper com tratamento amigável.
     */
    public function update($dados)
    {
        if (Validator::make($dados, $this->validationRules)) {
            return false;
        }

        try {
            $resultado = $this->db
                ->where($this->primaryKey, $dados[$this->primaryKey])
                ->updateComTratamento($dados);
            return $resultado > 0;
        } catch (\Exception $e) {
            $this->handleDatabaseError($e);
            return false;
        }
    }

    // ── filtroListagem sem alteração ─────────────────────────────────
    public function filtroListagem(array $post)
    {
        extract($post);
        $sql      = "select * from {$this->table}";
        $sqlparte = [];
        $params   = [];

        if (!empty(trim($filtroNomePessoa))) {
            array_push($sqlparte, "PES_NOME like :nomePessoa");
            $params['nomePessoa'] = "%{$filtroNomePessoa}%";
        }

        $sql .= (count($sqlparte) > 0) ? ' where ' . implode(' and ', $sqlparte) : '';

        $ordemPermitida = ['PES_NOME', 'CIDADE', 'UF'];
        $ordem = (isset($ordemPessoa) && in_array($ordemPessoa, $ordemPermitida))
            ? $ordemPessoa : 'PES_NOME';
        $sql .= " order by {$ordem} asc";

        $pdo = $this->db->dbSelect($sql, $params);
        return $this->db->dbBuscaArrayAll($pdo);
    }
}
