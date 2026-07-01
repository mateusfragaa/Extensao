<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use Core\Library\Session;

class Dashboard extends ControllerMain
{
    private $serviceRecebimento;

    public function __construct()
    {
        $this->loadHelper(['formHelper']);
        $this->serviceRecebimento = $this->loadService('Recebimento');
        return parent::__construct();
    }

    public function page($acao, $id_pedido)
    {
        $this->view('admin/PageDashboard',[],'sistema');
    }
}
