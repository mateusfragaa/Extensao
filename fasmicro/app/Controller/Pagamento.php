<?php

namespace App\Controller;

use App\Model\DespesaModel;
use App\Model\PessoaModel2;
use App\Model\TipoDocumentoModel2;
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

            $pessoaModel = new PessoaModel2;

            $pessoa = $pessoaModel->getById($despesa['PAG_FAVORECIDO_ID'] ?? null);

            $aDespesaStatus = $despesaModel->listaStatus;
        }

        $this->view("admin/lista{$this->controller}", [
            'despesa'        => $despesa,
            'pessoa'         => $pessoa,
            'aDespesaStatus' => $aDespesaStatus,
            'controller'     => $this->controller,
            'lista'          => $this->model->list($params),
        ], 'sistema');
    }

    public function form($action, $id = 0)
    {
        $params = $this->request->getGet();

        $despesaModel = new DespesaModel;

        $despesas = $despesaModel->getDespesasSelect();

        $tipoDocumentoModel = new TipoDocumentoModel2;

        $tiposDocumentos = $tipoDocumentoModel->lista(
            $tipoDocumentoModel->primaryKey
        );

        return $this->view("admin/form/form" . $this->controller, [
                'despesas'   => $despesas,
                "despesaSelecionada" => $params['despesa'] ?? null,
                'tiposDocumentos' => $tiposDocumentos,
                'controller' => $this->controller,
                'action'     => $this->action,
                'd'          => $this->model->getById($id),
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
}
