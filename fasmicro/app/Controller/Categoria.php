<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Categoria extends ControllerMain
{
    public function index()
    {
       return $this->view(
            "admin/listaCategoria",
            [
                'titulo' => "Lista de Categorias",
                "categorias" => $this->model->lista(),
                "aStatus" => $this->model->listaStatus 
            ]
        );
    }
}