<?php
namespace Core\Library;

class Csrf {
    /**
     * Gera um novo token CSRF e o armazena na sessão com um tempo de expiração.
     * Se um token já existe e é válido, ele é retornado. Caso contrário, um novo é gerado.
     * @return string O token CSRF gerado ou existente.
     */
    public static function generateToken() {
        if (isset($_SESSION[CSRF_TOKEN_NAME]) && $_SESSION[CSRF_TOKEN_NAME]["expire"] > time()) {
            return $_SESSION[CSRF_TOKEN_NAME]["token"];
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION[CSRF_TOKEN_NAME] = [
            'token' => $token,
            'expire' => time() + CSRF_EXPIRE
        ];
        return $token;
    }

    /**
     * Retorna o token CSRF atual da sessão.
     * Se não houver um token válido, um novo é gerado.
     * @return string O token CSRF atual.
     */
    public static function getToken() {
        return self::generateToken();
    }

    /**
     * Valida o token CSRF enviado na requisição POST.
     * @return bool True se o token for válido e não expirado, false caso contrário.
     */
    public static function validateToken() {
        if (!isset($_POST[CSRF_TOKEN_NAME]) || !isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false; // Token não presente na requisição ou na sessão
        }

        $postedToken = $_POST[CSRF_TOKEN_NAME];
        $sessionTokenData = $_SESSION[CSRF_TOKEN_NAME];

        // Verifica se o token expirou
        if ($sessionTokenData['expire'] <= time()) {
            unset($_SESSION[CSRF_TOKEN_NAME]); // Remove o token expirado
            return false; // Token expirado
        }

        // Compara o token enviado com o token da sessão de forma segura
        if (!hash_equals($sessionTokenData['token'], $postedToken)) {
            return false; // Token inválido
        }

        // Token válido, remove-o da sessão para evitar reuso (proteção one-time token)
        unset($_SESSION[CSRF_TOKEN_NAME]);
        return true;
    }
}
