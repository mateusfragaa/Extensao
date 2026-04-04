<?php

namespace App\Controller;
use Core\Library\ControllerMain;

class HomeSistema extends ControllerMain
{
    public function index()
    {
        $this->view('admin/main', [], 'sistema');
    }
}