<?php
namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Session;

class FaturarVenda extends ControllerMain
{
    private $serviceFaturar;

    public function __construct()
    {
        $this->loadHelper(['formHelper', 'faturarHelper']);
        $this->serviceFaturar = $this->loadService('FaturarVenda');
        return parent::__construct();
    }

    public function formFaturar($acao, $id_pedido)
    {
        
        switch ($acao) {
            case 'insert':
                $this->receber($_POST,$id_pedido);
                break;
        }
        
        
        $data = [];
        $data['info_venda'] = $this->serviceFaturar->dadosVenda($id_pedido);
        $data['formas_pagamento'] = $this->serviceFaturar->listaDocumento();
        $this->view(
            'admin/form/formFinalizacaoVenda',
            [
                "data" => $data
            ],
            'sistema'
        );
    }

    public function receber($post, $id_pedido) {
        $this->serviceFaturar->gravarRecebimento($post, $id_pedido);
    }


}
