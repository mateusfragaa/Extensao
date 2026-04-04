<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class TipoDocumento extends ControllerMain
{
    public function index()
    {
        $this->view('admin/listaTipoDocumento', [], 'sistema');
    }

    public function formTipoDocumento()
    {
        $this->view('admin/form/formTipoDocumento', [], 'sistema');
    }
}
