<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Auth extends ControllerMain
{
    public function __construct()
    {
        parent::__construct();
        $this->loadHelper('formHelper');
    }


    public function formLogin()
    {
        $this->view('public/login', [], 'login');
    }

    public function login()
    {
        $post = $this->getRotaParametros()['post'];
        $login = $post['USU_LOGIN'] ?? '';
        $senha = $post['USU_SENHA'] ?? '';

        // Buscar o usuário pelo Login ou E-mail
        // Vamos usar o model de usuário para isso
        $userModel = $this->loadModel('Usuario');

        // Buscamos no banco onde o login ou o email seja igual ao digitado
        $usuario = $userModel->db
            ->where(" (USU_LOGIN = ? OR USU_EMAIL = ?) ", [$login, $login])
            ->first();

        // Validações
        if (!$usuario) {
            \Core\Library\Session::set('msgError', 'Usuário não encontrado!');
            header("Location: /auth/formLogin");
            return;
        }

        if ($usuario['USU_STATUS'] == 0) {
            \Core\Library\Session::set('msgError', 'Esta conta está inativa!');
            header("Location: /auth/formLogin");
            return;
        }

        //Verifica a Senha
        if (password_verify($senha, $usuario['USU_SENHA'])) {
            // Senha correta! Salvamos o usuário na sessão
            \Core\Library\Session::set('usuario_logado', $usuario);

            \Core\Library\Session::set('msgSucesso', 'Bem-vindo, ' . $usuario['USU_NOME']);
            header("Location: /homeSistema"); // Redireciona para a tela interna
        } else {
            \Core\Library\Session::set('msgError', 'Senha incorreta!');
            header("Location: /auth/formLogin");
        }
    }
}
