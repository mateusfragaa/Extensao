<?php

namespace App\Controller;

use App\Model\DespesaModel;
use App\Model\PessoaModel;
use App\Model\TipoDocumentoModel;
use Core\Library\ControllerMain;
use Core\Library\Redirect;

class Pagamento extends ControllerMain
{
    public function index()
    {
        $this->loadHelper(['dateHelper', 'numberHelper']);

        $params = $this->request->getGet();

        $despesa = null;
        $pessoa = null;
        $aDespesaStatus = null;

        if (trim($params['despesa'] ?? '') != '') {
            $despesaModel = New DespesaModel;
            $despesa = $despesaModel->getById($params['despesa']);

            $pessoaModel = new PessoaModel;

            $pessoa = $pessoaModel->getById($despesa['PAG_FAVORECIDO_ID'] ?? null);

            $aDespesaStatus = $despesaModel->listaStatus;
        }

        $this->view("admin/lista{$this->controller}", [
            'despesa'        => $despesa,
            'pessoa'         => $pessoa,
            'aDespesaStatus' => $aDespesaStatus,
            'aStatus'        => $this->model->listaStatus,
            'controller'     => $this->controller,
            'lista'          => $this->model->list($params),
        ], 'sistema');
    }

    public function form($action, $id = 0)
    {
        $params = $this->request->getGet();

        $despesaModel = new DespesaModel;

        $despesas = $despesaModel->getDespesasSelect();

        $tipoDocumentoModel = new TipoDocumentoModel;

        $tiposDocumentos = $tipoDocumentoModel->lista(
            'TDC_ID'
        );

        $d =  $this->model->getById($id);

        $despesaSelecionada = $params['despesa'] ?? $d['PAGI_PAG_ID'] ?? null;

        return $this->view("admin/form/form" . $this->controller, [
                'despesas'   => $despesas,
                "despesaSelecionada" => $despesaSelecionada,
                'tiposDocumentos' => $tiposDocumentos,
                'controller' => $this->controller,
                'action'     => $this->action,
                'd'          => $d,
            ], 'sistema'
        );
    }

    public function insert()
    {
        $this->loadHelper('numberHelper');

        $result = $this->model->insert($this->request->getPost());

        if ($result['status'] === true) {
            return Redirect::page(
                $this->controller,
                ['msgSucesso' => "Registro inserido com sucesso."]
            );
        } else {
            return Redirect::page(
                $this->controller. "/form/" . $this->method . "/0",
                ['msgError' => $result['msgErro'] ?? "Falha ao inserir registro."]
            );
        }
    }

    public function update()
    {
        $post = $this->request->getPost();

        if ($this->model->cancelarRegistro($post)) {
            return Redirect::page(
                $this->controller . '',
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
