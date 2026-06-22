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

    public function buscarPorEmail(string $email)
    {
        return $this->db
            ->where('USU_EMAIL', $email)
            ->where('USU_STATUS', 1)
            ->first();
    }

    public function gerarTokenRecuperacao(int $idUsuario): string
    {
        $token       = bin2hex(random_bytes(32));
        $tokenHash   = hash('sha256', $token);
        $expiraEm    = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->db
            ->where($this->primaryKey, $idUsuario)
            ->update([
                'USU_RESET_TOKEN'  => $tokenHash,
                'USU_RESET_EXPIRA' => $expiraEm,
            ]);

        return $token;
    }

    public function buscarPorTokenValido(string $token)
    {
        $tokenHash = hash('sha256', $token);

        $usuario = $this->db
            ->where('USU_RESET_TOKEN', $tokenHash)
            ->first();

        if (empty($usuario)) {
            return [];
        }

        if (empty($usuario['USU_RESET_EXPIRA']) || strtotime($usuario['USU_RESET_EXPIRA']) < time()) {
            return [];
        }

        return $usuario;
    }

    public function redefinirSenha(int $idUsuario, string $senhaHash): bool
    {
        $linhas = $this->db
            ->where($this->primaryKey, $idUsuario)
            ->update([
                'USU_SENHA'        => $senhaHash,
                'USU_RESET_TOKEN'  => null,
                'USU_RESET_EXPIRA' => null,
            ]);

        return $linhas > 0;
    }

}
