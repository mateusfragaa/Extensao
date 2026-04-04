<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Pagamento extends ControllerMain
{
    public function index()
    {
        $this->view('admin/listaPagamento', [], 'sistema');
    }

    public function formPagamento()
    {
        $this->view('admin/form/formPagamento', [], 'sistema');
    }
}
