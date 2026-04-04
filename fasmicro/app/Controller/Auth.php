<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Auth extends ControllerMain
{
    public function formLogin()
    {   
        $this->view('public/login',[],'login');
    }
}