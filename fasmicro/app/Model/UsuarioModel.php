<?php

namespace App\Model;

use Core\Library\ModelMain;

class UsuarioModel extends ModelMain
{
    protected $table = 'tb_usuario';
    protected $primaryKey = "USU_ID";

    public $validationRules = [
        "USU_NOME"  => ["label" => "Nome Completo", "rules" => "required|min:3"],
        "USU_LOGIN" => ["label" => "Login", "rules" => "required|min:3"],
        "USU_EMAIL" => ["label" => "E-mail", "rules" => "email|required"], // Validação de e-mail pronta do seu framework
        "USU_NIVEL" => ["label" => "Nível de Acesso", "rules" => "required"],
        "USU_SENHA" => ["label" => "Senha", "rules" => "required|min:6"]
    ];

    public function isVazio()
    {
        // Buscamos a lista de usuários
        $usuarios = $this->lista('USU_NOME');

        // Se o resultado for vazio ou a contagem do array for zero, o banco está vazio
        return empty($usuarios);
    }
}
