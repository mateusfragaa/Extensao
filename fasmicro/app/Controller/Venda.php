<?php

namespace App\Controller;
use Core\Library\ControllerMain;

class Venda extends ControllerMain
{
    public function index()
    {
        $this->view('admin/listaVenda',[], 'sistema');
    }

    public function formVenda()
    {
        $this->view('admin/form/formVenda',[], 'sistema');
    }
}