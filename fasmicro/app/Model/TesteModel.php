<?php

namespace App\Model;

use Core\Library\ModelMain;

class TesteModel extends ModelMain
{
    protected $table = 'produtos';

    // Select e retorno de dados
    public function lista()
    {
        $pdo = $this->db->dbSelect("select * from {$this->table} where id = :id", ['id' => 1]);
        // Busca todos os dados
        return $this->db->dbBuscaDadosAll($pdo);
        // Busca somente o primeiro dado retornado
        return $this->db->dbBuscaDados($pdo);
    }

    public function somarId()
    {
        $pdo = $this->db->dbSelect("select sum(id) as soma from {$this->table} ", []);
        // Para quando for retornar somente um campo da consulta
        return $this->db->dbResultado($pdo,'soma');
    }

    public function inserirTeste($dados)
    {
        return $this->db->dbInsert("insert into {$this->table}(descricao)values(:descricao)",$dados);
    }

    public function atualizarTeste($dados)
    {
        return $this->db->dbUpdate(
            "update {$this->table} set descricao = :descricao where id = :id", $dados
        );
    }

    public function apagarTeste($dados)
    {
        return $this->db->dbUpdate(
            "delete from {$this->table} where id = :id", $dados
        );
    }

    public function primeiroTeste()
    {
        return $this->db->first();
    }

    public function todosTeste()
    {
        return $this->db->findAll();
    }

    public function insert($dados)
    {
        return $this->db->insert($dados);
    }

    public function update($dados)
    {
        return $this->db
        ->whereNotIn("id",[2,3,4,6])
        ->update($dados);
    }

    public function selectCustom()
    {
        
    }
    
}