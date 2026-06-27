<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use Core\Library\Session;

class BaixarRecebimento extends ControllerMain
{
    private $serviceRecebimento;

    public function __construct()
    {
        $this->loadHelper(['formHelper']);
        $this->serviceRecebimento = $this->loadService('Recebimento');
        return parent::__construct();
    }

    public function formBaixar($acao, $id_pedido)
    {
        $data = [];
        $data["recebimentos"] = $this->serviceRecebimento->lista_recebimentos();

        switch ($acao) {
            case 'insert':
                $data['recebimentos'] = $this->serviceRecebimento->gravarRecebimento($_POST, $id_pedido);
                break;
            case 'delete':
                $data['recebimentos'] = $this->serviceRecebimento->excluir_recebimento($_POST, $id_pedido);
                break;
            case 'finalizar':
                $data['recebimentos'] = $this->serviceRecebimento->finalizar_venda($id_pedido);
                break;
        }



        $data["status_rec"] = $this->serviceRecebimento->lista_status();

        $this->view(
            'admin/form/formFinalizacaoRecebimento',
            [],
            'sistema'
        );
    }
}
