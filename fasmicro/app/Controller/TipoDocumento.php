<?php

namespace App\Controller;

use Core\Library\ControllerMain;
use Core\Library\Redirect;
use Core\Library\Session;

class TipoDocumento extends ControllerMain
{
    /** Lista todos os tipos de documento. */
    public function index($action = null, $id = null)
    {
        $this->view(
            'admin/listaTipoDocumento',
            ['tiposDocumento' => $this->model->lista('TDC_DESCRICAO')],
            'sistema'
        );
    }

    /** Filtragem via AJAX ou requisição normal. */
    public function filtroListagemTipoDocumento($action = null, $id = null)
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
               && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        $this->view(
            'admin/listaTipoDocumento',
            ['tiposDocumento' => $this->model->filtroListagem($this->request->getPost())],
            $isAjax ? null : 'sistema'
        );
    }

    /** Prepara formulário para insert / update / delete / view. */
    public function formTipoDocumento($acao = 'view', $id = null)
    {
        $formInputs = Session::getDestroy('formInputs');

        if ($acao === 'insert') {
            $data = ['action_form' => 'insert', 'tipoDocumento' => $formInputs ?: []];
        } else {
            $data = [
                'action_form'   => $acao,
                'tipoDocumento' => $formInputs ?: $this->model->getById($id),
            ];
        }

        $this->view('admin/form/formTipoDocumento', ['data' => $data], 'sistema');
    }

    /** Insere novo tipo de documento. */
    public function insert($action = null, $id = null)
    {
        $post = $this->request->getPost();
        unset($post['TDC_ID']);

        $idGerado = $this->model->insert($post);

        if ($idGerado) {
            Redirect::page('tipoDocumento/', ['msgSucesso' => 'Tipo de Documento cadastrado com sucesso!']);
        } else {
            $msgError = Session::get('msgError') ?: 'Verifique os campos obrigatórios e tente novamente.';
            Redirect::page('tipoDocumento/formTipoDocumento/insert', ['msgError' => $msgError]);
        }
    }

    /** Atualiza tipo de documento existente. */
    public function update($action = null, $id = null)
    {
        $post = $this->request->getPost();

        if ($this->model->update($post)) {
            Redirect::page('tipoDocumento/', ['msgSucesso' => 'Tipo de Documento atualizado com sucesso.']);
        } else {
            $msgError = Session::get('msgError') ?: 'Erro ao atualizar. Verifique os dados informados.';
            Redirect::page('tipoDocumento/formTipoDocumento/update/' . $post['TDC_ID'], ['msgError' => $msgError]);
        }
    }

    /** Exclui tipo de documento. */
    public function delete($action = null, $id = null)
    {
        $post = $this->request->getPost();

        if ($this->model->delete($post)) {
            Redirect::page('tipoDocumento/', ['msgSucesso' => 'Tipo de Documento excluído com sucesso.']);
        } else {
            $msgError = Session::get('msgError') ?: 'Erro ao excluir. Verifique se há vínculos com outros registros.';
            Redirect::page('tipoDocumento/', ['msgError' => $msgError]);
        }
    }
}
