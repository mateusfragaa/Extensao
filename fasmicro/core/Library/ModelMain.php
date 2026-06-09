<?php

namespace Core\Library;

use Core\Library\Request;

class ModelMain
{
    public $db;
    public $validationRules = [];
    protected $table;
    protected $primaryKey = "";

    public function __construct()
    {
        $this->db = new Database(
            $_ENV['DB_CONNECTION'],
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_DATABASE'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );

        // Setando a tabela do model
        $this->db->table($this->table);

    }

    /**
     * Undocumented function
     *
     * @param string $orderBy
     * @return array
     */
    public function lista($orderBy = "descricao")
    {
        return $this->db
            ->orderBy($orderBy)
            ->findAll();
    }

    /**
     * Undocumented function
     *
     * @param int $id
     * @return array
     */
    public function getById($id)
    {
        if ($id == 0) {
            return [];
        } else {
            return $this->db->where($this->primaryKey, $id)->first();
        }
    }

    /**
     * Undocumented function
     *
     * @param array $dados
     * @return bool
     */
    public function insert($dados)
    {
        if (Validator::make($dados, $this->validationRules)) {
            return false;
        } else {
            try {
                unset($dados[$this->primaryKey]);        // excluir a key id do array

                $idGerado = $this->db->insert($dados);
                if ($idGerado > 0) {
                    return $idGerado;
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                $this->handleDatabaseError($e);
                return false;
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param array $dados
     * @return bool
     */
    public function update($dados)
    {
        if (Validator::make($dados, $this->validationRules)) {
            return false;
        } else {
            try {
                if (
                    $this->db
                    ->where($this->primaryKey, $dados[$this->primaryKey])
                    ->update($dados) > 0
                ) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                $this->handleDatabaseError($e);
                return false;
            }
        }
    }

    /**
     * Trata erros de banco de dados para mensagens amigáveis
     */
    protected function handleDatabaseError(\Exception $e)
    {
        $message = $e->getMessage();
        
        // Erro 1062: Duplicate entry
        if (strpos($message, '1062') !== false) {
            if (strpos($message, 'CPF_CNPJ') !== false) {
                Session::set('msgError', 'Este CPF ou CNPJ já está cadastrado no sistema.');
            } else if (strpos($message, 'EMAIL') !== false) {
                Session::set('msgError', 'Este e-mail já está em uso por outro registro.');
            } else {
                Session::set('msgError', 'Já existe um registro com esses dados únicos no sistema.');
            }
        } else {
            Session::set('msgError', 'Erro ao processar a operação no banco de dados. Tente novamente.');
        }
    }


    /**
     * Undocumented function
     *
     * @param array $dados
     * @return bool
     */
    public function delete($dados)
    {
        if (
            $this->db
            ->where($this->primaryKey, $dados[$this->primaryKey])
            ->delete() > 0
        ) {
            return true;
        } else {
            return false;
        }
    }
}
