<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Recebimento extends ControllerMain
{
    public function index()
    {
        $this->view('admin/listaRecebimento', [], 'sistema');
    }

    public function formRecebimento()
    {
        $this->view('admin/form/formRecebimento', [], 'sistema');
    }
}
