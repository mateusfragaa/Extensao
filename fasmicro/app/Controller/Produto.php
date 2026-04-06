<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Session;

class Produto extends ControllerMain
{
    public function index(bool $temFiltro)
    {
        if (!$temFiltro) {
            $this->view(
                'admin/listaProduto',
                [
                    "produtos" => $this->model->find()
                ],
                'sistema'
            );
        }else {
            $this->view(
                'admin/listaProduto',
                [
                    "produtos" => $this->model->filtroListagem($_POST)
                ],
                'sistema'
            );
        }
    }

    public function filtroListagemProduto()
    {
        $this->index(true);
    }

    public function formProduto($acao,$id)
    {
        switch ($acao) {
            case 'criar':
                $this->view(
                    'admin/form/formProduto',
                    [
                        "action_form" => "/produto/cadastro"
                    ],
                    'sistema'
                );
            case 'visualizar':
                $this->view(
                    'admin/form/formProduto',
                    [
                        "produto" => $this->model->find($id),
                        "action_form" => "#"
                    ],
                    'sistema'
                );
            case 'editar':
                Session::set('idProduto',$id);
                $this->view(
                    'admin/form/formProduto',
                    [
                        "produto" => $this->model->find($id),
                        "action_form" => "/produto/editar"
                    ],
                    'sistema'
                );
            case 'deletar':
                Session::set('idProduto', $id);
                $this->view(
                    'admin/form/formProduto',
                    [
                        "produto" => $this->model->find($id),
                        "action_form" => "/produto/deletar"
                    ],
                    'sistema'
                );
        }
    }

    public function editar()
    {
        if ($this->model->update($_POST, Session::getDestroy('idProduto'))) {
            var_dump("Deu certo");
        } else {
            var_dump(Session::getDestroy('msgErrorForm'));
            var_dump(Session::getDestroy('msgError'));
        }
    }

    public function deletar()
    {
        
        if ($this->model->delete(Session::getDestroy('idProduto'))) {
            var_dump("Deu certo");
        } else {
            var_dump(Session::getDestroy('msgErrorForm'));
            var_dump(Session::getDestroy('msgError'));
        }
    }
    

    public function cadastro()
    {
        if ($this->model->insert($_POST)) {
            var_dump("Deu certo");
        }else {
            var_dump(Session::getDestroy('msgErrorForm'));
            var_dump(Session::getDestroy('msgError'));
        }
    }
}
