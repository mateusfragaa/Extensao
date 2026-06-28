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

        switch ($acao) {
            case 'insert':
                $data['recebimentos'] = $this->serviceRecebimento->gravarRecebimento($_POST, $id_pedido);
                break;
            case 'delete':
                $this->serviceRecebimento->apagar_recebimento_item($_POST);
                break;
            case 'receber':
                $this->serviceRecebimento->receber_recebimento($_POST);
                break;
        }

        $data = [];
        $data["recebimentos"] = $this->serviceRecebimento->lista_recebimentos();
        $data["recebimentos_baixa"] = $this->serviceRecebimento->lista_recebimentos_baixa();
        $data["recebimentos_item"] = $this->serviceRecebimento->lista_recebimentos_itens();
        $data["documentos"] = $this->serviceRecebimento->lista_tipo_documento();
        $data["status_rec"] = $this->serviceRecebimento->lista_status();
        $this->view(
            'admin/form/formFinalizacaoRecebimento',
            ["data" => $data],
            'sistema'
        );
    }
}
