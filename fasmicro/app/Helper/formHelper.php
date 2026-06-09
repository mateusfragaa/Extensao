<?php

use Core\Library\Request;
use Core\Library\Session;


if (!function_exists('formDadosInput')) {

    /*
    * Função para carregar os dados na função _view() para usar a setValue e carregar o action nos formulários
    */
    function formDadosInput($data, $key)
    {
        if (isset($data['data'][$key])) {
            _viewData($data['data'][$key]);
        }
        return $data['data']['action_form'] ?? null;
    }
}

if (!function_exists('formSubTitulo')) {

    /**
     * Subtítulo que ficará no topo do formulário indicando a ação que vai acontecer
     * ao enviar o formulário
     *
     * @param string $action
     * @return string
     */
    function formSubTitulo($action)
    {
        if ($action == "insert") {
            return ' - Novo';
        } elseif ($action == "update") {
            return ' - Alteração';
        } elseif ($action == "delete") {
            return ' - Exclusão';
        } elseif ($action == "view") {
            return ' - Visualização';
        } else {
            return '';
        }
    }
}

if (!function_exists('exibeAlerta')) {

    /**
     * Exibe alertas Bootstrap vindos da sessão.
     * CORREÇÃO: Session::get() retorna false (não "") quando a chave não existe,
     * portanto usamos comparação estrita com false.
     *
     * @return string
     */
    function exibeAlerta()
    {
        $msgSucesso = Session::getDestroy('msgSucesso');
        $msgError   = Session::getDestroy('msgError');
        $msgAlerta  = Session::getDestroy('msgAlerta');

        $mensagem   = '';
        $classAlerta = '';
        $icone      = '';

        if ($msgSucesso !== false && $msgSucesso !== '') {
            $mensagem    = $msgSucesso;
            $classAlerta = 'success';
            $icone       = '<i class="bi bi-check-circle-fill me-2"></i>';
        } elseif ($msgError !== false && $msgError !== '') {
            $mensagem    = $msgError;
            $classAlerta = 'danger';
            $icone       = '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
        } elseif ($msgAlerta !== false && $msgAlerta !== '') {
            $mensagem    = $msgAlerta;
            $classAlerta = 'warning';
            $icone       = '<i class="bi bi-info-circle-fill me-2"></i>';
        }

        if ($mensagem === '') {
            return '';
        }

        return '<div class="alert alert-' . $classAlerta . ' alert-dismissible fade show d-flex align-items-center" role="alert">'
            . $icone
            . '<span>' . htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') . '</span>'
            . '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fechar"></button>'
            . '</div>';
    }
}
