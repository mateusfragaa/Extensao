<?php

namespace Core\Library;

/**
 * Implementação manual de JWT com algoritmo HS256.
 * Não depende de biblioteca externa.
 */
class Jwt
{
    /**
     * secret
     *
     * @return string
     */
    private static function secret(): string
    {
        $secret = Ambiente::get('JWT_SECRET');

        if (empty($secret)) {
            ApiResponse::serverError('JWT_SECRET não configurado no .env');
        }

        return $secret;
    }

    /**
     * encode - Gera um token JWT assinado com HS256.
     *
     * @param array $payload Dados a incluir no token (não sensíveis)
     * @param int|null $expireSeconds Tempo de expiração em segundos (padrão: JWT_EXPIRE do .env ou 3600)
     */
    public static function encode(array $payload, ?int $expireSeconds = null): string
    {
        $expire = $expireSeconds ?? (int)(Ambiente::get('JWT_EXPIRE') ?: 3600);

        $header = self::base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ]));

        $payload['iat'] = time();
        $payload['exp'] = time() + $expire;

        $encodedPayload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE));

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$encodedPayload", self::secret(), true)
        );

        return "$header.$encodedPayload.$signature";
    }

    /**
     * decode - Decodifica e valida um token JWT.
     *
     * @param string $token
     * @return array Payload decodificado
     * @throws \RuntimeException Em caso de token inválido ou expirado
     */
    public static function decode(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new \RuntimeException('Token malformado');
        }

        [$header, $payload, $signature] = $parts;

        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", self::secret(), true)
        );

        // Comparação segura contra timing attacks
        if (!hash_equals($expectedSignature, $signature)) {
            throw new \RuntimeException('Assinatura inválida');
        }

        $data = json_decode(self::base64UrlDecode($payload), true);

        if (!is_array($data)) {
            throw new \RuntimeException('Payload inválido');
        }

        if (isset($data['exp']) && $data['exp'] < time()) {
            throw new \RuntimeException('Token expirado');
        }

        return $data;
    }

    /**
     * encodeRefresh
     * 
     * Gera um token de refresh (longa duração, sem dados de sessão).
     */
    public static function encodeRefresh(int $userId, ?int $expireSeconds = null): string
    {
        $expire = $expireSeconds ?? (int)(Ambiente::get('JWT_REFRESH_EXPIRE') ?: 604800); // 7 dias

        return self::encode(['sub' => $userId, 'type' => 'refresh'], $expire);
    }

    /**
     * base64UrlEncode
     *
     * @param string $data
     * @return string
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * base64UrlDecode
     *
     * @param string $data
     * @return string
     */
    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
    }
}
