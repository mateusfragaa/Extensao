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
            ->where("USU_LOGIN", $login)
            ->orWhere("USU_EMAIL", $login)
            ->first();

        // Validações
        if (!$usuario) {
            \Core\Library\Session::set('msgError', 'Usuário não encontrado!');
            header("Location: /auth/formLogin");
            return;
        }

        if ($usuario['USU_STATUS'] == 0) {
            \Core\Library\Session::set('msgError', 'Esta conta está inativa. Entre em contato com um administrador.');
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

    public function logout()
    {
        \Core\Library\Session::destroy('usuario_logado');
        \Core\Library\Session::destroy('msgSucesso');
        \Core\Library\Session::destroy('msgError');

        header("Location: /auth/formLogin");
        exit;
    }

    public function formEsqueciSenha()
    {
        $this->view('public/esqueciSenha', [], 'login');
    }

    public function esqueciSenha()
    {
        $post  = $this->getRotaParametros()['post'];
        $email = trim($post['USU_EMAIL'] ?? '');

        $msgPadrao = 'Se o e-mail informado estiver cadastrado em nosso sistema, '
            . 'você receberá em instantes uma mensagem com instruções para redefinir sua senha.';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            \Core\Library\Session::set('msgError', 'Informe um e-mail válido.');
            header("Location: /auth/formEsqueciSenha");
            exit;
        }

        $userModel = $this->loadModel('Usuario');
        $usuario   = $userModel->buscarPorEmail($email);

        if ($usuario) {
            $token = $userModel->gerarTokenRecuperacao((int) $usuario['USU_ID']);
            $link  = rtrim(baseUrl(), '/') . '/auth/formResetSenha/' . $token;

            $corpo = '<p>Olá, ' . htmlspecialchars($usuario['USU_NOME'], ENT_QUOTES, 'UTF-8') . '!</p>'
                . '<p>Recebemos uma solicitação para redefinir a senha da sua conta.</p>'
                . '<p><a href="' . $link . '" target="_blank">Clique aqui para criar uma nova senha</a></p>'
                . '<p>Este link é válido por 1 hora. Se você não solicitou a redefinição, apenas ignore este e-mail.</p>';

            \Core\Library\Mailer::enviar($usuario['USU_EMAIL'], $usuario['USU_NOME'], 'Recuperação de senha', $corpo);
        }

        \Core\Library\Session::set('msgSucesso', $msgPadrao);
        header("Location: /auth/formLogin");
        exit;
    }

    public function formResetSenha($token = '')
    {
        if (empty($token)) {
            \Core\Library\Session::set('msgError', 'Link de recuperação inválido.');
            header("Location: /auth/formLogin");
            exit;
        }

        $userModel = $this->loadModel('Usuario');
        $usuario   = $userModel->buscarPorTokenValido($token);

        if (empty($usuario)) {
            \Core\Library\Session::set('msgError', 'Este link de recuperação é inválido ou já expirou. Solicite um novo.');
            header("Location: /auth/formEsqueciSenha");
            exit;
        }

        $this->view('public/resetSenha', ['token' => $token], 'login');
    }

    public function resetSenha()
    {
        $post      = $this->getRotaParametros()['post'];
        $token     = $post['token'] ?? '';
        $senha     = $post['USU_SENHA'] ?? '';
        $confirmar = $post['CONFIRMAR_SENHA'] ?? '';

        if (empty($token)) {
            \Core\Library\Session::set('msgError', 'Link de recuperação inválido.');
            header("Location: /auth/formLogin");
            exit;
        }

        $userModel = $this->loadModel('Usuario');
        $usuario   = $userModel->buscarPorTokenValido($token);

        if (empty($usuario)) {
            \Core\Library\Session::set('msgError', 'Este link de recuperação é inválido ou já expirou. Solicite um novo.');
            header("Location: /auth/formEsqueciSenha");
            exit;
        }

        if ($senha !== $confirmar) {
            \Core\Library\Session::set('msgError', 'As senhas não conferem!');
            header("Location: /auth/formResetSenha/" . $token);
            exit;
        }

        $erroForca = \App\Config\PasswordConfig::validar($senha);
        if ($erroForca !== true) {
            \Core\Library\Session::set('msgError', $erroForca);
            header("Location: /auth/formResetSenha/" . $token);
            exit;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $userModel->redefinirSenha((int) $usuario['USU_ID'], $senhaHash);

        \Core\Library\Session::set('msgSucesso', 'Senha redefinida com sucesso! Faça login com sua nova senha.');
        header("Location: /auth/formLogin");
        exit;
    }
}
