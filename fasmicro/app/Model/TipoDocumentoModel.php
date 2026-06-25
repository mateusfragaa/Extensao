<?php

namespace App\Model;

use Core\Library\ModelMain;
use Core\Library\Session;
use Core\Library\Validator;

class TipoDocumentoModel extends ModelMain
{
    protected $table      = 'tb_tipo_documento';
    protected $primaryKey = 'TDC_ID';

    public $titulo      = 'Tipo de Documento';
    public $listaStatus = [1 => 'Ativo', 0 => 'Inativo'];
    public $parcela = [4];
    public $validationRules = [
        'TDC_DESCRICAO' => [
            'label' => 'Descrição',
            'rules' => 'required|max:45'
        ],
        'TDC_STATUS' => [
            'label' => 'Status',
            'rules' => 'required|in:0,1'
        ],
    ];

    

    public function getParcela()
    {
        return $this->parcela;
    }

    // ──────────────────────────────────────────────────────────────────
    // insert — remove PK e trata erro amigável após chamada ao Database
    // ──────────────────────────────────────────────────────────────────

    public function insert($dados)
    {
        if (Validator::make($dados, $this->validationRules)) {
            return false;
        }

        unset($dados[$this->primaryKey]);
        $idGerado = $this->db->insert($dados);

        if ($idGerado > 0) {
            return $idGerado;
        }

        // Database.php seta msgError técnico no catch — substituímos pela mensagem amigável
        $this->substituirMsgErrorAmigavel();
        return false;
    }

    // ──────────────────────────────────────────────────────────────────
    // update — separa PK do SET e trata erro amigável
    // ──────────────────────────────────────────────────────────────────

    public function update($dados)
    {
        if (Validator::make($dados, $this->validationRules)) {
            return false;
        }

        $id         = $dados[$this->primaryKey];
        $dadosSemPK = $dados;
        unset($dadosSemPK[$this->primaryKey]);

        $resultado = $this->db
            ->where($this->primaryKey, $id)
            ->update($dadosSemPK);

        if ($resultado >= 0) {
            return true;
        }

        $this->substituirMsgErrorAmigavel();
        return false;
    }

    // ──────────────────────────────────────────────────────────────────
    // filtroListagem
    // ──────────────────────────────────────────────────────────────────

    public function filtroListagem(array $post): array
    {
        $sql      = "SELECT * FROM {$this->table}";
        $sqlParte = [];
        $params   = [];

        $busca = trim($post['filtroDescricao'] ?? '');
        if ($busca !== '') {
            $sqlParte[]            = 'TDC_DESCRICAO LIKE :descricao';
            $params['descricao']   = '%' . $busca . '%';
        }

        if (isset($post['filtroStatus']) && $post['filtroStatus'] !== '') {
            $sqlParte[]          = 'TDC_STATUS = :status';
            $params['status']    = (int) $post['filtroStatus'];
        }

        if (!empty($sqlParte)) {
            $sql .= ' WHERE ' . implode(' AND ', $sqlParte);
        }

        $sql .= ' ORDER BY TDC_DESCRICAO ASC';

        $pdo = $this->db->dbSelect($sql, $params);
        return $this->db->dbBuscaArrayAll($pdo);
    }

    // ──────────────────────────────────────────────────────────────────
    // Erros amigáveis
    // ──────────────────────────────────────────────────────────────────

    /**
     * O Database.php do framework seta msgError com a mensagem técnica do PDO
     * no próprio catch — o handleDatabaseError nunca é chamado nesse caso.
     * Este método lê o que o Database setou, detecta o código de erro
     * e substitui pela mensagem amigável correta.
     */
    protected function substituirMsgErrorAmigavel(): void
    {
        $msgAtual = Session::get('msgError') ?? '';

        if (strpos($msgAtual, '1062') !== false) {
            Session::set('msgError', 'Já existe um Tipo de Documento com essa descrição. Escolha outro nome.');
            return;
        }

        if (strpos($msgAtual, '1451') !== false) {
            Session::set('msgError', 'Não é possível excluir: este tipo está vinculado a pagamentos ou recebimentos.');
            return;
        }

        if (strpos($msgAtual, '1048') !== false) {
            Session::set('msgError', 'Um campo obrigatório não foi preenchido. Verifique os dados e tente novamente.');
            return;
        }

        // Se já tem uma mensagem amigável (sem código SQL), mantém como está
        if (!preg_match('/SQLSTATE|1\d{3}/', $msgAtual)) {
            return;
        }

        Session::set('msgError', 'Erro ao processar a operação. Tente novamente.');
    }
}

