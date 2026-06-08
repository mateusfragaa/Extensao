<?php

namespace Core\Library;

/**
 * Logger — Sistema de logging estruturado compatível com PSR-3.
 *
 * Níveis suportados (do mais ao menos severo):
 *   emergency > alert > critical > error > warning > notice > info > debug
 *
 * Saída: storage/logs/app-YYYY-MM-DD.log
 * Nível mínimo configurável via LOG_LEVEL no .env (padrão: debug).
 *
 * Formato de cada entrada:
 *   [2026-06-06 14:30:15] app.ERROR: mensagem {"chave":"valor"}
 */
class Logger
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    private static array $weights = [
        self::DEBUG     => 0,
        self::INFO      => 1,
        self::NOTICE    => 2,
        self::WARNING   => 3,
        self::ERROR     => 4,
        self::CRITICAL  => 5,
        self::ALERT     => 6,
        self::EMERGENCY => 7,
    ];

    public static function emergency(string $message, array $context = []): void
    {
        self::log(self::EMERGENCY, $message, $context);
    }

    public static function alert(string $message, array $context = []): void
    {
        self::log(self::ALERT, $message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        self::log(self::CRITICAL, $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log(self::ERROR, $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log(self::WARNING, $message, $context);
    }

    public static function notice(string $message, array $context = []): void
    {
        self::log(self::NOTICE, $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log(self::INFO, $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log(self::DEBUG, $message, $context);
    }

    /**
     * log - Grava uma entrada no arquivo de log se o nível for suficiente.
     *
     * @param string $level   Um dos níveis PSR-3 (use as constantes desta classe)
     * @param string $message Mensagem; suporta interpolação via {chave} do contexto
     * @param array  $context Dados extras serializados como JSON ao final da linha
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        if (!self::shouldLog($level)) {
            return;
        }

        $interpolated = self::interpolate($message, $context);
        $contextJson  = empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $timestamp    = date('Y-m-d H:i:s');
        $entry        = "[{$timestamp}] app." . strtoupper($level) . ": {$interpolated}{$contextJson}" . PHP_EOL;

        $logFile = self::resolveLogFile();
        $dir     = dirname($logFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * resolveLogFile - Retorna o caminho do arquivo de log do dia atual.
     */
    public static function resolveLogFile(): string
    {
        $base = defined('PATHAPP') ? rtrim(PATHAPP, DIRECTORY_SEPARATOR) : '.';
        return $base . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs'
             . DIRECTORY_SEPARATOR . 'app-' . date('Y-m-d') . '.log';
    }

    /**
     * shouldLog - Verifica se o nível é igual ou superior ao mínimo configurado.
     */
    private static function shouldLog(string $level): bool
    {
        $minLevel  = strtolower($_ENV['LOG_LEVEL'] ?? self::DEBUG);
        $minWeight = self::$weights[$minLevel] ?? 0;
        $weight    = self::$weights[$level]    ?? 0;

        return $weight >= $minWeight;
    }

    /**
     * interpolate - Substitui {chave} na mensagem pelos valores do contexto (PSR-3).
     */
    private static function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_string($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = (string) $val;
            }
        }
        return strtr($message, $replace);
    }
}
