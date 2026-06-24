<?php
namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
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
        $data = [];
        $data['recebimentos'] = $this->serviceFaturar->busca_recebimento_venda($id_pedido);
        
        switch ($acao) {
            case 'insert':
                $data['recebimentos'] = $this->serviceFaturar->gravarRecebimento($_POST, $id_pedido);
                break;
            case 'delete':
                $data['recebimentos'] = $this->serviceFaturar->excluir_recebimento($_POST, $id_pedido);
                break;
            case 'finalizar':
                $data['recebimentos'] = $this->serviceFaturar->finalizar_venda($id_pedido);
                break;
        }
        
        

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

}
