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
            unset($dados[$this->primaryKey]);        // excluir a key id do array

            $idGerado = $this->db->insert($dados);
            if ($idGerado > 0) {
                return $idGerado;
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
    public function update($dados)
    {
        if (Validator::make($dados, $this->validationRules)) {
            return false;
        } else {
            if (
                $this->db
                ->where($this->primaryKey, $dados[$this->primaryKey])
                ->update($dados) > 0
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
