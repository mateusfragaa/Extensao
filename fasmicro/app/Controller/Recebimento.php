<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;

class Recebimento extends ControllerMain
{
    private $recebimentoService;
    public function __construct()
    {
        $this->loadHelper(['formHelper','recebimentoHelper']);
        $this->recebimentoService = $this->loadService('recebimento');
        return parent::__construct();
    }

    public function index()
    {
        $data = [];
        $data['metricas'] = $this->recebimentoService->getMetricas();
        $data["recebimentos"] = $this->model->buscar_recebimento_completo();
        $data["status_rec"] = $this->model->getStatus();

        $this->view(
            'admin/listaRecebimento',
            ['data' => $data],
            'sistema'
        );
    }

    public function formRecebimento($acao, $id)
    {
        $data = [];
        switch ($acao) {
            case 'insert':
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $this->insert($_POST);
                    return;
                }
                break;
            case 'update':
                $data["action"] = "update";

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->update($_POST);
                    return;
                }
                break;
            case 'delete':
                $data["action"] = "delete";

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->delete($id);
                    return;
                }
                break;
            case 'cancelar':
                $data["action"] = "cancelar";

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->delete($id);
                    return;
                }
                break;
        }
        

        if ($acao != 'insert') $data["recebimento"] = $this->model->getById($id);

        $data['pessoas'] = $this->recebimentoService->lista_pessoa();
        $data['documentos'] = $this->recebimentoService->lista_tipo_documento();
        $data['status'] = $this->recebimentoService->lista_status();

        $this->view(
            'admin/form/formRecebimento',
            [
                "data" => $data
            ],
            'sistema'
        );
    }

    public function update($post)
    {
        $dados = [
            'REC_ID' => $post['REC_ID'],
            'rec_valor' => $post['rec_valor'],
            'rec_status' => $post['rec_status'],
            'rec_observacao' => $post['rec_observacao'],
            'rec_devedor_id' => $post['rec_devedor_id'],
            'rec_vencimento' => $post['rec_vencimento'],
            'rec_data_baixa' => $post['rec_data_baixa'],
            'rec_tipo_documento_id' => $post['rec_tipo_documento_id']
        ];

        if ($this->recebimentoService->update($dados)) {
            Redirect::page(
                "recebimento/",
                ['msgSucesso' => 'Recebimento atualizado com sucesso.']
            );
        } else {
            Redirect::page(
                "recebimento/formRecebimento/update/" . $post['REC_ID'],
                ['msgError' => 'Erro ao atualizar o recebimento.']
            );
        }
    }

    public function delete($id)
    {
        if ($this->recebimentoService->delete($id)) {
            Redirect::page("recebimento/", ['msgSucesso' => 'Sucesso ao apagar o registro']);
        } else {
            Redirect::page("recebimento/", ['msgError' => 'Erro ao apagar registro, verifique se os dados estão corretos!']);
        }
    }

    public function insert($post)
    {
        $post = [
            'rec_valor' => $post['rec_valor'],
            'rec_status' => $post['rec_status'],
            'rec_observacao' => $post['rec_observacao'],
            'rec_devedor_id' => $post['rec_devedor_id'],
            'rec_vencimento' => $post['rec_vencimento'],
            'rec_tipo_documento_id' => $post['rec_tipo_documento_id']
        ];
        $idGerado = $this->model->insert2($post);

        if ($idGerado) {
            Redirect::page('recebimento/formRecebimento/', ['msgSucesso' => 'Sucesso ao inserir registro, novo produto : ' . $idGerado]);
        } else {
            Redirect::page('recebimento/', ['msgError' => 'Erro ao gravar recebimento']);
        }
    }
}
