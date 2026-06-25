<?php

namespace Core\Library;

/**
 * Proteção CSRF via Synchronizer Token Pattern.
 *
 * Gera um token criptograficamente seguro, armazena na sessão e o valida
 * em toda requisição que muta estado (POST/PUT/PATCH/DELETE).
 * O token também fica disponível como header X-CSRF-Token para chamadas AJAX.
 */
class Csrf
{
    private const SESSION_KEY      = 'csrf_token';
    private const SESSION_TIME_KEY = 'csrf_token_time';

    /**
     * Gera um novo token e o armazena na sessão com timestamp.
     */
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set(self::SESSION_KEY, $token);
        Session::set(self::SESSION_TIME_KEY, time());
        return $token;
    }

    /**
     * Retorna o token atual. Gera um novo se ausente ou expirado.
     */
    public static function getToken(): string
    {
        $token = Session::get(self::SESSION_KEY);
        $time  = Session::get(self::SESSION_TIME_KEY);

        if (!$token || !$time || (time() - (int) $time) > (int) CSRF_EXPIRE) {
            return self::generate();
        }

        return $token;
    }

    /**
     * Retorna o nome do campo/cookie configurado (CSRF_TOKEN_NAME).
     */
    public static function getTokenName(): string
    {
        return CSRF_TOKEN_NAME;
    }

    /**
     * Retorna o HTML do campo hidden pronto para inserção em formulários.
     */
    public static function getHiddenField(): string
    {
        $name  = htmlspecialchars(self::getTokenName(), ENT_QUOTES, 'UTF-8');
        $value = htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="' . $name . '" value="' . $value . '">';
    }

    /**
     * Valida o token recebido contra o token armazenado na sessão.
     *
     * Usa hash_equals() para comparação em tempo constante, prevenindo
     * timing attacks. Retorna false se ausente, expirado ou não confere.
     */
    public static function validate(?string $token): bool
    {
        $stored     = Session::get(self::SESSION_KEY);
        $storedTime = Session::get(self::SESSION_TIME_KEY);

        $valid = $stored
            && $storedTime
            && (time() - (int) $storedTime) <= (int) CSRF_EXPIRE
            && $token !== null
            && $token !== ''
            && hash_equals($stored, $token);

        if (!$valid) {
            return false;
        }

        if (CSRF_REGENERATE) {
            self::generate();
        }

        return true;
    }

    /**
     * Verifica se a URI atual está na lista de exclusão CSRF_EXCLUDE_URIS.
     */
    public static function isExcluded(): bool
    {
        $excludes = CSRF_EXCLUDE_URIS;

        if (empty($excludes)) {
            return false;
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';

        foreach ($excludes as $pattern) {
            $pattern = trim($pattern);
            if ($pattern !== '' && strpos($uri, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
