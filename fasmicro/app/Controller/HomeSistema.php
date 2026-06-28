<?php

namespace App\Controller;
use Core\Library\ControllerMain;

class HomeSistema extends ControllerMain
{

    public function __construct()
    {
        $this->loadHelper(['formHelper']);
        return parent::__construct();
    }

    public function index()
    {
        $this->view('admin/main', [], 'sistema');
    }
}