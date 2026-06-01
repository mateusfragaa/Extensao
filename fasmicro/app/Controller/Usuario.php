<?php

namespace App\Controller;

use Core\Library\ControllerMain;

class Usuario extends ControllerMain
{
    public function __construct()
    {
        parent::__construct(); // Chama o construtor do framework
        $this->loadHelper('formHelper'); // Carrega as funções de alerta e formulário
    }


    public function index()
    {
        // O Model busca todos os usuários ordenados pelo nome
        $usuarios = $this->model->lista('USU_NOME');

        // Enviamos o array usuarios para a View
        $this->view('admin/listaUsuario', ['usuarios' => $usuarios], 'sistema');

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

    // ============================================================
    //      VALIDAÇÃO DE SENHA OBRIGATÓRIA PARA NOVOS USUÁRIOS
    // ============================================================
    if ($id == 0 && empty($post['USU_SENHA'])) {
        \Core\Library\Session::set('msgError', 'A senha é obrigatória para novos usuários!');

        // Salva tudo o que o usuário digitou
        \Core\Library\Session::set('formInputs', $post); 

        header("Location: /usuario/formUsuario");
        return; 
    }
    

    $dados = [
        'USU_ID'     => $id,
        'USU_NOME'   => $post['USU_NOME'] ?? '',
        'USU_LOGIN'  => $post['USU_LOGIN'] ?? '',
        'USU_EMAIL'  => $post['USU_EMAIL'] ?? '',
        'USU_NIVEL'  => $post['USU_NIVEL'] ?? '',
        'USU_STATUS' => ($post['USU_STATUS'] ?? '') == 'ativo' ? 1 : 0
    ];

    if (!empty($post['USU_SENHA'])) {
        if ($post['USU_SENHA'] !== $post['CONFIRMAR_SENHA']) {
            \Core\Library\Session::set('msgError', 'As senhas não conferem!');

            // Salva tudo o que o usuário digitou
            \Core\Library\Session::set('formInputs', $post); 

            header("Location: /usuario/formUsuario/" . $id);
            return;
        }
        $dados['USU_SENHA'] = password_hash($post['USU_SENHA'], PASSWORD_DEFAULT);
    }

    if ($id > 0) {
        if (empty($post['USU_SENHA'])) {
            unset($this->model->validationRules['USU_SENHA']);
        }
        $resultado = $this->model->update($dados);
        $mensagem = "Usuário atualizado!";
    } else {
        $resultado = $this->model->insert($dados);
        $mensagem = "Usuário criado com sucesso!";
    }

    
    
    if ($resultado) {
        \Core\Library\Session::set('msgSucesso', $mensagem);
        header("Location: /usuario");
    } else { 
        //var_dump(\Core\Library\Session::get('msgError')); die();  
        \Core\Library\Session::set('msgError', 'Erro ao processar.');
        header("Location: /usuario/formUsuario/" . $id);
    }
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
