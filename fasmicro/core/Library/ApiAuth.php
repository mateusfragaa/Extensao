<?php

namespace Core\Library;

class ApiAuth
{
    /**
     * required - Exige autenticação válida. Encerra com 401 se o token for inválido.
     *
     * @return array Payload do token (sub, nivel, iat, exp, ...)
     */
    public static function required(): array
    {
        $token = self::extractToken();

        if ($token === null) {
            ApiResponse::unauthorized('Token não fornecido. Use o header Authorization: Bearer <token>');
        }

        try {
            $payload = Jwt::decode($token);
        } catch (\RuntimeException $e) {
            ApiResponse::unauthorized($e->getMessage());
        }

        return $payload;
    }

    /**
     * requiredWithLevel - Exige autenticação com nível mínimo de acesso.
     * 
     * Menor número = maior privilégio (1 = super admin, 21 = usuário comum).
     *
     * @param int $nivelMinimo
     * @return array Payload do token
     */
    public static function requiredWithLevel(int $nivelMinimo): array
    {
        $payload = self::required();

        $nivel = (int)($payload['nivel'] ?? PHP_INT_MAX);

        if ($nivel > $nivelMinimo) {
            ApiResponse::forbidden('Nível de acesso insuficiente para este recurso');
        }

        return $payload;
    }

    /**
     * optional - Verifica autenticação opcional. Retorna o payload ou null sem encerrar.
     *
     * @return array|null
     */
    public static function optional(): ?array
    {
        $token = self::extractToken();

        if ($token === null) {
            return null;
        }

        try {
            return Jwt::decode($token);
        } catch (\RuntimeException) {
            return null;
        }
    }

    /**
     * extractToken - Extrai o Bearer token do header Authorization.
     *
     * @return string|null
     */
    private static function extractToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';

        if (empty($header) && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $header  = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (preg_match('/^Bearer\s+(.+)$/i', trim($header), $matches)) {
            return $matches[1];
        }

        return null;
    }
}
