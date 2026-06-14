<?php

namespace App\Model;

use Core\Library\ModelMain;
use Core\Library\DatabasePessoa;
use Core\Library\Validator;
use Core\Library\Session;

class PessoaModel extends ModelMain
{
    protected $table      = 'tb_pessoa';
    protected $primaryKey = 'PES_ID';

    // Propriedades esperadas pelo ControllerMain do fasmicro (compatibilidade)
    public $titulo      = 'Pessoa';
    public $listaStatus = [];

    public $validationRules = [
        'PES_NOME' => [
            'label' => 'Nome Completo / Razão Social',
            'rules' => 'required|min:3|max:45'
        ],
        'CPF_CNPJ' => [
            'label' => 'CPF / CNPJ',
            'rules' => 'required|cpf_cnpj'
        ],
        'EMAIL' => [
            'label' => 'E-mail',
            'rules' => 'required|email|max:50'
        ],
        'TIPO_PESSOA' => [
            'label' => 'Tipo de Pessoa',
            'rules' => 'required'
        ],
    ];

    /**
     * Substitui $this->db por DatabasePessoa que captura erros amigáveis
     * (CPF/CNPJ duplicado, e-mail duplicado) sem alterar o Database.php original.
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

    // ──────────────────────────────────────────────────────────────────
    // CRUD — sobrescrita para tratamento de erro amigável
    // ──────────────────────────────────────────────────────────────────

    /**
     * Insere pessoa e retorna o ID gerado ou false em caso de falha.
     *
     * @param array $dados
     * @return int|false
     */
    public function insert($dados)
    {
        if (Validator::make($dados, $this->validationRules)) {
            return false;
        }

        try {
            unset($dados[$this->primaryKey]); // garante AUTO_INCREMENT
            $idGerado = $this->db->insertComTratamento($dados);
            return $idGerado > 0 ? $idGerado : false;
        } catch (\Exception $e) {
            $this->handleDatabaseError($e);
            return false;
        }
    }

    /**
     * Atualiza pessoa. Retorna true/false.
     *
     * @param array $dados
     * @return bool
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

            return $resultado >= 0; // >= 0 pois 0 linhas afetadas ainda é sucesso (dados iguais)
        } catch (\Exception $e) {
            $this->handleDatabaseError($e);
            return false;
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Listagem com filtro
    // ──────────────────────────────────────────────────────────────────

    /**
     * Filtra pessoas com base nos campos enviados pelo formulário de busca.
     *
     * @param array $post
     * @return array
     */
    public function filtroListagem(array $post): array
    {
        $sql      = "SELECT * FROM {$this->table}";
        $sqlParte = [];
        $params   = [];

        if (!empty(trim($post['filtroNomePessoa'] ?? ''))) {
            $sqlParte[]            = 'PES_NOME LIKE :nomePessoa';
            $params['nomePessoa']  = '%' . trim($post['filtroNomePessoa']) . '%';
        }

        if (!empty(trim($post['filtroCpfCnpj'] ?? ''))) {
            $cpfCnpjLimpo         = preg_replace('/\D/', '', $post['filtroCpfCnpj']);
            $sqlParte[]           = 'CPF_CNPJ LIKE :cpfCnpj';
            $params['cpfCnpj']    = '%' . $cpfCnpjLimpo . '%';
        }

        if (!empty($post['filtroTipoPessoa'] ?? '')) {
            $sqlParte[]                = 'TIPO_PESSOA = :tipoPessoa';
            $params['tipoPessoa']      = $post['filtroTipoPessoa'];
        }

        if (!empty($post['filtroUF'] ?? '')) {
            $sqlParte[]        = 'UF = :uf';
            $params['uf']      = $post['filtroUF'];
        }

        if (!empty($sqlParte)) {
            $sql .= ' WHERE ' . implode(' AND ', $sqlParte);
        }

        $ordemPermitida = ['PES_NOME', 'CIDADE', 'UF', 'TIPO_PESSOA'];
        $ordem = in_array($post['ordemPessoa'] ?? '', $ordemPermitida)
            ? $post['ordemPessoa']
            : 'PES_NOME';

        $sql .= " ORDER BY {$ordem} ASC";

        $pdo = $this->db->dbSelect($sql, $params);
        return $this->db->dbBuscaArrayAll($pdo);
    }

    // ──────────────────────────────────────────────────────────────────
    // Tratamento de erros de banco
    // ──────────────────────────────────────────────────────────────────

    /**
     * Converte erros técnicos de banco em mensagens compreensíveis para o usuário.
     */
    protected function handleDatabaseError(\Exception $e): void
    {
        $message = $e->getMessage();

        // Duplicate entry (MySQL 1062)
        if (strpos($message, '1062') !== false) {
            if (strpos($message, 'CPF_CNPJ') !== false) {
                Session::set('msgError', 'Este CPF/CNPJ já está cadastrado no sistema.');
            } elseif (strpos($message, 'EMAIL') !== false) {
                Session::set('msgError', 'Este e-mail já está em uso por outro registro.');
            } else {
                Session::set('msgError', 'Já existe um registro com esses dados únicos.');
            }
            return;
        }

        // Foreign key constraint (MySQL 1451 — ao tentar excluir pessoa vinculada)
        if (strpos($message, '1451') !== false) {
            Session::set('msgError', 'Não é possível excluir: esta pessoa está vinculada a pedidos, pagamentos ou recebimentos.');
            return;
        }

        // Genérico
        Session::set('msgError', 'Erro ao processar a operação no banco de dados. Tente novamente.');
    }
}
