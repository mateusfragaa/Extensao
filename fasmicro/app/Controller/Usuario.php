<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Usuario extends ControllerMain
{
    public function __construct()
    {
        parent::__construct(); // Chama o construtor do framework
        $this->loadHelper(['formHelper', 'usuarioHelper']); // Carrega as funções de alerta e formulário
    }


    public function index()
    {
        // O Model busca todos os usuários ordenados pelo nome
        $usuarios = $this->model->lista('USU_NOME');

        // Conta quantos registros retornaram no array
        $totalUsuarios = count($usuarios);

        // Enviamos o array usuarios para a View
        $this->view('admin/listaUsuario', ['usuarios' => $usuarios, 'total' => $totalUsuarios], 'sistema');

        //$this->view('admin/listaUsuario', [], 'sistema');
    }

    public function formUsuario($id = 0)
    {
        $dadosUsuario = [];

        // Se for edição, busca do banco
        if ($id > 0) {
            $dadosUsuario = $this->model->getById($id);
        }

        // Se houver dados de um erro anterior, recupera e mescla
        $oldInputs = \Core\Library\Session::getDestroy('formInputs');
        if ($oldInputs) {
            // O array_merge faz com que o que foi digitado preencha o que veio do banco
            $dadosUsuario = array_merge($dadosUsuario, $oldInputs);
        }

        // Enviamos os dados para a View
        $this->view('admin/form/formUsuario', ['usuario' => $dadosUsuario], 'sistema');
    }

    public function salvar()
    {
        $post = $this->getRotaParametros()['post'];
        $id = $post['USU_ID'];


        // Se for um novo usuário, a senha é obrigatória
        if ($id == 0 && empty($post['USU_SENHA'])) {
            \Core\Library\Session::set(
                'msgError',
                'Por favor, informe uma senha válida!'
            );

            \Core\Library\Session::set('formInputs', $post);

            header("Location: /usuario/formUsuario/0");
            exit;
        }

        // Prepara os dados
        $dados = [
            'USU_ID'     => $id,
            'USU_NOME'   => $post['USU_NOME'] ?? '',
            'USU_LOGIN'  => $post['USU_LOGIN'] ?? '',
            'USU_EMAIL'  => $post['USU_EMAIL'] ?? '',
            'USU_NIVEL'  => $post['USU_NIVEL'] ?? '',
            'USU_STATUS' => ($post['USU_STATUS'] ?? '') == 'ativo' ? 1 : 0
        ];

        // Tratamento da senha
        if (!empty($post['USU_SENHA'])) {

            if ($post['USU_SENHA'] !== $post['CONFIRMAR_SENHA']) {

                \Core\Library\Session::set(
                    'msgError',
                    'As senhas não conferem!'
                );

                \Core\Library\Session::set(
                    'formInputs',
                    $post
                );

                header("Location: /usuario/formUsuario/" . $id);
                exit;
            }

            $dados['USU_SENHA'] = password_hash(
                $post['USU_SENHA'],
                PASSWORD_DEFAULT
            );
        } elseif ($id > 0) {

            // Em edição, se a senha estiver vazia,
            // remove a validação da senha
            unset($this->model->validationRules['USU_SENHA']);
        }

        // FORÇAR PRIMEIRO USUÁRIO COMO ADMIN
        if ($this->model->isVazio()) {
            $dados['USU_NIVEL'] = 'admin';
            $dados['USU_STATUS'] = 1; // Ativo
        }

        // Salva no banco
        if ($id > 0) {
            $resultado = $this->model->update($dados);
            $mensagem = "Usuário atualizado com sucesso!";
        } else {
            $resultado = $this->model->insert($dados);
            $mensagem = "Usuário cadastrado com sucesso!";
        }

        if ($resultado) {

            \Core\Library\Session::set(
                'msgSucesso',
                $mensagem
            );

            header("Location: /usuario");
        } else {

            \Core\Library\Session::set(
                'msgError',
                'Preencha todos os campos obrigatórios!'
            );

            \Core\Library\Session::set(
                'formInputs',
                $post
            );

            header("Location: /usuario/formUsuario/" . $id);
        }

        exit;
    }
    
    public function excluir($id)
    {
        $dados['USU_ID'] = $id;
        if ($this->model->delete($dados)) {
            \Core\Library\Session::set('msgSucesso', 'Usuário removido com sucesso!');

            // Redireciona de volta para a lista
            header("Location: /usuario");
        } else {
            \Core\Library\Session::set('msgError', 'Erro ao tentar excluir o usuário.');

            // Redireciona de volta para a lista
            header("Location: /usuario");
        }
    }
}
