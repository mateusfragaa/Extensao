<?php

namespace App\Controller;

use App\Model\PessoaModel;
use Core\Library\ControllerMain;
use Core\Library\Redirect;

class Despesa extends ControllerMain
{
    public function index()
    {
        $this->loadHelper(['dateHelper', 'numberHelper']);

        $params = $this->request->getGet();

        $resumo = [
            'totalPagarMes' => $this->model->getTotalPagarMes(),
            'venceHoje'     => $this->model->getVenceHoje(),
            'debitosAtraso' => $this->model->getDebitosAtraso(),
        ];

        $this->view('admin/listaDespesa', [
            'resumo'     => $resumo,
            'controller' => $this->controller,
            'lista'      => $this->model->list($params),
            'aStatus'    => $this->model->listaStatus,
        ], 'sistema');
    }

    public function form($action, $id = 0)
    {
        $pessoaModel = new PessoaModel;

        return $this->view("admin/form/form" . $this->controller, [
                'pessoas'    => $pessoaModel->db->findAll(),
                "d"          => $this->model->getById($id),
                'controller' => $this->controller,
                'action'     => $this->action,
            ], 'sistema'
        );
    }

    public function insert()
    {
        $params = $this->request->getPost();

        $params['PAG_VALOR_ABERTO'] = $params['PAG_VALOR'] ?? 0;

        if ($this->model->insert($params)) {
            return Redirect::page(
                $this->controller . '?status=A',
                ['msgSucesso' => "Registro inserido com sucesso."]
            );
        } else {
            return Redirect::page(
                $this->controller. "/form/" . $this->method . "/0",
                ['msgError' => "Falha ao inserir registro."]
            );
        }
    }

    public function update()
    {
        $post = $this->request->getPost();

        if ($this->model->cancelarRegistro($post)) {
            return Redirect::page(
                $this->controller . '?status=A',
                ['msgSucesso' => "Registro atualizado com sucesso."]
            );
        } else {
            return Redirect::page(
                $this->controller . '/form/' . $this->method . '/' . $post[$this->model->primaryKey],
                ['msgError' => "Falha ao atualizar registro."]
            );
        }
    }
}
