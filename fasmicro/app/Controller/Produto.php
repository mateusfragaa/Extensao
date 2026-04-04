<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Produto extends ControllerMain
{
    public function index()
    {
        $this->view('admin/listaProduto', [], 'sistema');
    }

    public function formProduto()
    {
        $this->view('admin/form/formProduto', [], 'sistema');
    }
}
