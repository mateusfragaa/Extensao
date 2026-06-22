<?php

namespace App\Config;

/**
 * Configuração dos requisitos de força de senha da aplicação.
 *
 * Para ajustar as regras, basta alterar os valores estáticos abaixo.
 * O front-end (jsPasswordStrength) e o back-end (UsuarioModel) lêem
 * esta classe automaticamente — nenhuma outra alteração é necessária.
 */
class PasswordConfig
{
    /** Comprimento mínimo exigido (0 = sem mínimo). */
    public static int $minLength = 8;

    /** Exige pelo menos uma letra maiúscula (A-Z). */
    public static bool $requireUppercase = true;

    /** Exige pelo menos uma letra minúscula (a-z). */
    public static bool $requireLowercase = true;

    /** Exige pelo menos um dígito numérico (0-9). */
    public static bool $requireNumber = true;

    /** Exige pelo menos um caractere especial (qualquer não-alfanumérico). */
    public static bool $requireSpecial = true;

    public static function getConfig(): array
    {
        return [
            'minLength'        => self::$minLength,
            'requireUppercase' => self::$requireUppercase,
            'requireLowercase' => self::$requireLowercase,
            'requireNumber'    => self::$requireNumber,
            'requireSpecial'   => self::$requireSpecial,
        ];
    }

    public static function validar(string $senha)
    {
        if (self::$minLength > 0 && strlen($senha) < self::$minLength) {
            return "A senha deve ter no mínimo " . self::$minLength . " caracteres.";
        }

        if (self::$requireUppercase && !preg_match('/[A-Z]/', $senha)) {
            return "A senha deve conter ao menos uma letra maiúscula.";
        }

        if (self::$requireLowercase && !preg_match('/[a-z]/', $senha)) {
            return "A senha deve conter ao menos uma letra minúscula.";
        }

        if (self::$requireNumber && !preg_match('/[0-9]/', $senha)) {
            return "A senha deve conter ao menos um número.";
        }

        if (self::$requireSpecial && !preg_match('/[^a-zA-Z0-9]/', $senha)) {
            return "A senha deve conter ao menos um caractere especial.";
        }

        return true;
    }
}