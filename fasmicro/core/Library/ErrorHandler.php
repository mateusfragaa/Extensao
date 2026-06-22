<?php

namespace Core\Library;

use Core\Library\Logger;

/**
 * ErrorHandler — Tratamento centralizado de erros e exceções.
 *
 * Registra handlers globais para:
 *   - Exceções não capturadas (set_exception_handler)
 *   - Erros PHP convertidos em ErrorException (set_error_handler)
 *   - Erros fatais no shutdown (register_shutdown_function)
 *
 * Em contexto de API (/api/...) devolve JSON padronizado.
 * Em contexto web exibe uma página de erro HTML.
 */
class ErrorHandler
{
    /**
     * register - Registra todos os handlers globais. Chamar uma vez no index.php.
     *
     * @return void
     */
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * handleException - Handler para exceções não capturadas.
     *
     * @param \Throwable $e
     * @return void
     */
    public static function handleException(\Throwable $e): void
    {
        $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? (int) $e->getCode() : 500;

        $level = $code >= 500 ? Logger::CRITICAL : Logger::WARNING;
        Logger::log($level, $e->getMessage(), [
            'code'    => $code,
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'uri'     => $_SERVER['REQUEST_URI'] ?? '',
            'method'  => $_SERVER['REQUEST_METHOD'] ?? '',
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
            'trace'   => $e->getTraceAsString(),
        ]);

        if (self::isApiRequest()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'status'  => 'error',
                'code'    => $code,
                'message' => self::isDebug()
                    ? $e->getMessage()
                    : 'Erro interno do servidor.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code($code);
        self::renderHtml($code, self::isDebug() ? $e->getMessage() : null, $e);
    }

    /**
     * handleError - Converte erros PHP em ErrorException.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @return bool
     * @throws \ErrorException
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        throw new \ErrorException($errstr, $errno, $errno, $errfile, $errline);
    }

    /**
     * handleShutdown - Captura erros fatais que não chegam ao exception handler.
     *
     * @return void
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            self::handleException(
                new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
            );
        }
    }

    /**
     * isApiRequest - Detecta se a requisição atual é para a API.
     *
     * @return bool
     */
    private static function isApiRequest(): bool
    {
        $uri = ltrim($_REQUEST['parametros'] ?? ($_SERVER['REQUEST_URI'] ?? ''), '/');
        return str_starts_with($uri, 'api/') || str_contains($uri, '/api/');
    }

    /**
     * isDebug - Retorna true se a aplicação está em modo debug (APP_DEBUG=true no .env).
     *
     * @return bool
     */
    private static function isDebug(): bool
    {
        $val = $_ENV['APP_DEBUG'] ?? 'false';
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * renderHtml - Exibe a página de erro HTML.
     *
     * @param int             $code
     * @param string|null     $detail  Mensagem detalhada (só em debug)
     * @param \Throwable|null $e
     * @return void
     */
    private static function renderHtml(int $code, ?string $detail = null, ?\Throwable $e = null): void
    {
        $titles = [
            400 => 'Requisição Inválida',
            401 => 'Não Autenticado',
            403 => 'Acesso Negado',
            404 => 'Página Não Encontrada',
            405 => 'Método Não Permitido',
            419 => 'Token de Segurança Expirado',
            500 => 'Erro Interno do Servidor',
        ];

        $title   = $titles[$code] ?? 'Erro';
        $message = $detail ?? 'Ocorreu um erro inesperado. Por favor, tente novamente.';

        // Bloco de stack trace somente em modo debug
        $trace = '';
        if ($e !== null && self::isDebug()) {
            $safeTrace = htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8');
            $safeFile  = htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8');
            $trace = <<<HTML
            <div class="card border-danger mt-4">
                <div class="card-header bg-danger text-white">Stack Trace (APP_DEBUG=true)</div>
                <div class="card-body p-3">
                    <p class="mb-1 text-muted small">{$safeFile} : linha {$e->getLine()}</p>
                    <pre class="mb-0 small" style="white-space:pre-wrap;">{$safeTrace}</pre>
                </div>
            </div>
            HTML;
        }

        $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Erro {$code} — FasMicro</title>
            <link rel="stylesheet" href="/assests/bootstrap/css/bootstrap.min.css">
        </head>
        <body class="bg-light">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-body text-center py-5">
                                <h1 class="display-1 text-danger fw-bold">{$code}</h1>
                                <h2 class="mb-3">{$title}</h2>
                                <p class="text-muted mb-4">{$safeMessage}</p>
                                <a href="/" class="btn btn-primary">Voltar ao início</a>
                            </div>
                        </div>
                        {$trace}
                    </div>
                </div>
            </div>
        </body>
        </html>
        HTML;
        exit;
    }
}
