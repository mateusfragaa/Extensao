<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Usuario extends ControllerMain
{
    public function index()
    {
        $this->view('admin/listaUsuario', [], 'sistema');
    }

    public function formUsuario()
    {
        $this->view('admin/form/formUsuario', [], 'sistema');
    }
}
