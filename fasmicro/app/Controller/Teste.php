<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Teste extends ControllerMain
{
    public function print($dados)
    {
        echo '<pre>';
        print_r($dados);
        echo '</pre>';
    }

    public function index() :void
    {
        $this->print($this->model->lista());
    }

    public function inserir(): void
    {
        $this->print($this->model->inserirTeste(['descricao' => 'inserindoTeste']));
    }

    public function atualizar(): void
    {
        $this->print($this->model->atualizarTeste(['descricao' => 'atualizarTeste', 'id' => 4]));
    }

    public function apagar(): void
    {
        $this->print($this->model->apagarTeste(['id' => 7]));
    }

    public function soma(): void
    {
        $this->print($this->model->somarId());
    }

    public function primeiro(): void
    {
        $this->print($this->model->primeiroTeste());
    }

    public function todosTeste(): void
    {
        $this->print($this->model->todosTeste());
    }

    public function updateD(): void
    {
        $this->print($this->model->update(['descricao' => 'teste 05']));
    }

    public function selectCustom()
    {
        $this->print($this->model->selectCustom());
    }

}