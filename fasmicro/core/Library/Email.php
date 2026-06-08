<?php

namespace Core\Library;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    static function enviarEmail(
        string $emailRemetente,
        string $nomeRemetente,
        string $assunto,
        string $corpoEmail,
        string $destinatario,
        array $aAnexos = []
    ): bool {
        if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("E-mail destinatário inválido: {$destinatario}");
        }

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->SMTPDebug  = 0;
        $mail->CharSet    = 'UTF-8';
        $mail->SMTPAuth   = (bool) $_ENV['MAIL.SMTPAuth'];
        $mail->SMTPSecure = $_ENV['MAIL.SMTPSECURE'];
        $mail->Host       = $_ENV['MAIL.HOST'];
        $mail->Port       = (int) $_ENV['MAIL.PORT'];
        $mail->Username   = $_ENV['MAIL.USER'];
        $mail->Password   = $_ENV['MAIL.SENHA'];
        $mail->From       = $emailRemetente ?: $_ENV['MAIL.USER'];
        $mail->FromName   = $nomeRemetente  ?: $_ENV['MAIL.NOME'];

        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $corpoEmail;
        $mail->AltBody = strip_tags($corpoEmail);

        $count = count($aAnexos['name'] ?? []);
        for ($i = 0; $i < $count; $i++) {
            $mail->addAttachment($aAnexos['tmp_name'][$i], $aAnexos['name'][$i]);
        }

        $mail->send();
        return true;
    }
}
