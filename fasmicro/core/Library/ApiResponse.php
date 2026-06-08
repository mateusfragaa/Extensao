<?php

namespace Core\Library;

class ApiResponse
{
    /**
     * Envia resposta de sucesso com dados.
     *
     * @param mixed $data
     * @param int $code
     * @param array|null $meta Paginação ou metadados extras
     */
    public static function success(mixed $data = null, int $code = 200, ?array $meta = null): void
    {
        $body = [
            'status' => 'success',
            'code'   => $code,
            'data'   => $data,
        ];

        if ($meta !== null) {
            $body['meta'] = $meta;
        }

        self::send($body, $code);
    }

    /**
     * Envia resposta de erro genérico.
     *
     * @param string $message
     * @param int $code
     * @param array|null $errors Erros de validação campo a campo
     */
    public static function error(string $message, int $code = 400, ?array $errors = null): void
    {
        $body = [
            'status'  => 'error',
            'code'    => $code,
            'message' => $message,
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        self::send($body, $code);
    }

    /**
     * created - 201 Created
     *
     * @param mixed $data
     * @return void
     */
    public static function created(mixed $data = null): void
    {
        self::success($data, 201);
    }

    /**
     * noContent - 204 No Content
     *
     * @return void
     */
    public static function noContent(): void
    {
        self::send(null, 204);
    }

    /** 400 Bad Request */
    public static function badRequest(string $message = 'Requisição inválida', ?array $errors = null): void
    {
        self::error($message, 400, $errors);
    }

    /**
     * unauthorized - 401 Unauthorized
     *
     * @param string $message
     * @return void
     */
    public static function unauthorized(string $message = 'Não autenticado'): void
    {
        self::error($message, 401);
    }

    /**
     * forbidden - 403 Forbidden
     *
     * @param string $message
     * @return void
     */
    public static function forbidden(string $message = 'Acesso negado'): void
    {
        self::error($message, 403);
    }

    /**
     * notFound - 404 Not Found
     *
     * @param string $message
     * @return void
     */
    public static function notFound(string $message = 'Recurso não encontrado'): void
    {
        self::error($message, 404);
    }

    /**
     * methodNotAllowed - 405 Method Not Allowed
     *
     * @param string $message
     * @return void
     */
    public static function methodNotAllowed(string $message = 'Método não permitido'): void
    {
        self::error($message, 405);
    }

    /**
     * validationError - 422 Unprocessable Entity — erros de validação
     *
     * @param array $errors
     * @param string $message
     * @return void
     */
    public static function validationError(array $errors, string $message = 'Dados inválidos'): void
    {
        self::error($message, 422, $errors);
    }

    /**
     * serverError - 500 Internal Server Error
     *
     * @param string $message
     * @return void
     */
    public static function serverError(string $message = 'Erro interno do servidor'): void
    {
        self::error($message, 500);
    }

    /**
     * send - Envia a resposta JSON e encerra a execução.
     *
     * @param mixed $body
     * @param int $code
     */
    private static function send(mixed $body, int $code): void
    {
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=UTF-8');
            header('X-Content-Type-Options: nosniff');
        }

        if ($body !== null) {
            echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        exit;
    }
}
