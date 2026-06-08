<?php

namespace Core\Library;

class ModelMain
{
    public $db;
    public $validationRules = [];
    public $primaryKey = "id";
    public $titulo = '';
    public $listaStatus = [];
    
    protected $table;

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
            return $this->db->where("id", $id)->first();
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
            unset($dados[$this->primaryKey]);        // excluir a key id do array

            if ($this->db->insert($dados) > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Valida, insere e retorna o ID do novo registro.
     * Retorna 0 em caso de falha de validação ou erro de banco.
     */
    public function insertGetId(array $dados): int
    {
        if (Validator::make($dados, $this->validationRules)) {
            return 0;
        }

        unset($dados[$this->primaryKey]);
        return (int) $this->db->insert($dados);
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
            if ($this->db
                ->where($this->primaryKey, $dados[$this->primaryKey])
                ->update($dados) >= 0
            ) {
                return true;
            } else {
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
    public function delete($dados) 
    {
        if ($this->db
            ->where($this->primaryKey, $dados[$this->primaryKey])
            ->delete() > 0
        ) {
            return true;
        } else {
            return false;
        }
    }
}