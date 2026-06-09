<?php

use Core\Library\Request;
use Core\Library\Session;
use Core\Library\Csrf;

/**
 * Gera um campo de input oculto com o token CSRF.
 * Deve ser usado dentro de formulários POST.
 * @return string
 */
function csrfField(): string {
    if (!CSRF_ENABLE) {
        return "";
    }
    $token = Csrf::getToken();
    $name = CSRF_TOKEN_NAME;
    return "<input type=\"hidden\" name=\"$name\" value=\"$token\">";
}

if (!function_exists('formDadosInput')) {

    /*
    * Função para carregar os dados na função _view() para usar a setValue e carregar o action nos formulários
    */
    function formDadosInput($data, $key)
    {
// ver se essa mudança vai poder ser permanente.
    if (isset($data['data'][$key])) {
        _viewData($data['data'][$key]);
    }
    return $data['data']['action_form'] ?? null;
}
//.
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
     * Undocumented function
     *
     * @return string
     */
    function exibeAlerta()
    {
        $msgSucesso = Session::getDestroy('msgSucesso');
        $msgError = Session::getDestroy('msgError');
        $msgAlerta = Session::getDestroy('msgAlerta');

        $mensagem = '';
        $classAlerta = '';

        if ($msgSucesso != "") {
            $mensagem = $msgSucesso;
            $classAlerta = 'success';
        } elseif ($msgError != "") {
            $mensagem = $msgError;
            $classAlerta = 'danger';
        } elseif ($msgAlerta != "") {
            $mensagem = $msgAlerta;
            $classAlerta = 'warning';
        }

        if ($mensagem == "") {
            return "";
        } else {
            return '<div class="alert alert-' . $classAlerta . ' alert-dismissible fade show" role="alert">
                        <strong>' . $mensagem . '</strong>.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }
    }
}