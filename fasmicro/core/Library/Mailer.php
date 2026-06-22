<?php

namespace Core\Library;

/**
 * Mailer — Envio de e-mails usando as configurações SMTP definidas no .env
 * (MAIL.HOST, MAIL.SMTPAuth, MAIL.PORT, MAIL.SMTPSECURE, MAIL.NOME, MAIL.USER, MAIL.SENHA).
 *
 * Uso preferencial: PHPMailer (composer require phpmailer/phpmailer).
 * Caso a biblioteca não esteja instalada, faz fallback para a função mail()
 * nativa do PHP (não recomendado para Gmail, que exige autenticação SMTP).
 */
class Mailer
{
    public static function enviar(string $destinatarioEmail, string $destinatarioNome, string $assunto, string $corpoHtml): bool
    {
        if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
            return self::enviarComPhpMailer($destinatarioEmail, $destinatarioNome, $assunto, $corpoHtml);
        }

        return self::enviarComMailNativo($destinatarioEmail, $destinatarioNome, $assunto, $corpoHtml);
    }

    private static function enviarComPhpMailer(string $destinatarioEmail, string $destinatarioNome, string $assunto, string $corpoHtml): bool
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL.HOST'] ?? '';
            $mail->SMTPAuth   = filter_var($_ENV['MAIL.SMTPAuth'] ?? true, FILTER_VALIDATE_BOOLEAN);
            $mail->Username   = $_ENV['MAIL.USER'] ?? '';
            $mail->Password   = $_ENV['MAIL.SENHA'] ?? '';
            $mail->SMTPSecure = $_ENV['MAIL.SMTPSECURE'] ?? 'tls';
            $mail->Port       = (int) ($_ENV['MAIL.PORT'] ?? 587);
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($_ENV['MAIL.USER'] ?? '', $_ENV['MAIL.NOME'] ?? 'Sistema');
            $mail->addAddress($destinatarioEmail, $destinatarioNome);

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = $corpoHtml;
            $mail->AltBody = strip_tags($corpoHtml);

            $mail->send();
            return true;
        } catch (\Exception $e) {
            Logger::error('Falha ao enviar e-mail via PHPMailer: ' . $mail->ErrorInfo);
            return false;
        }
    }

    private static function enviarComMailNativo(string $destinatarioEmail, string $destinatarioNome, string $assunto, string $corpoHtml): bool
    {
        $remetenteNome  = $_ENV['MAIL.NOME'] ?? 'Sistema';
        $remetenteEmail = $_ENV['MAIL.USER'] ?? 'no-reply@localhost';

        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: {$remetenteNome} <{$remetenteEmail}>" . "\r\n";

        $enviado = @mail($destinatarioEmail, $assunto, $corpoHtml, $headers);

        if (!$enviado) {
            Logger::error('Falha ao enviar e-mail via mail() nativo para ' . $destinatarioEmail);
        }

        return $enviado;
    }
}