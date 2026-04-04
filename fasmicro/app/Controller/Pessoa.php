<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Pessoa extends ControllerMain
{
    public function index()
    {
        $this->view('admin/listaPessoa', [], 'sistema');
    }

    public function formPessoa()
    {
        $this->view('admin/form/formPessoa', [], 'sistema');
    }
}
