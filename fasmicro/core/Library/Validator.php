<?php

namespace Core\Library;

class Validator
{
    public static function make(array $data, array $rules)
    {
        $errors = null;

        foreach ($rules as $ruleKey => $ruleValue) {

            $itensRule = explode("|", $ruleValue['rules']);

            if (isset($data[$ruleKey])) {

                foreach ($itensRule as $itemKey) {

                    $items = explode(":", $itemKey);

                    switch ($items[0]) {

                        case 'required':

                            if (($data[$ruleKey] == "") || (empty($data[$ruleKey]))) {
                                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> deve ser preenchido.";
                            }

                            break;

                        case 'email':

                            if (!filter_var($data[$ruleKey], FILTER_VALIDATE_EMAIL)) {
                                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> não é válido.";
                            }

                            break;

                        case 'float':

                            if (!filter_var($data[$ruleKey], FILTER_VALIDATE_FLOAT)) {
                                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> deve conter número decimal.";
                            }

                            break;

                        case 'int':

                            if (!filter_var($data[$ruleKey], FILTER_VALIDATE_INT)) {
                                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> deve conter número inteiro.";
                            }

                            break;

                        case "min":

                            if (strlen(strip_tags($data[$ruleKey])) < $items[1]) {
                                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> dever conter um mínimo " . $items[1] . " caracteres.";
                            }

                            break;

                        case 'max':

                            if (strlen(strip_tags($data[$ruleKey])) > $items[1]) {
                                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> dever conter um maximo " . $items[1] . " caracteres.";
                            }

                            break;
                        // Adição de validação para CPF/CNPJ
                        case 'cpf_cnpj':
                            $val = preg_replace('/\D/', '', $data[$ruleKey]);
                            $tipo = $data['TIPO_PESSOA'] ?? 'F';

                            if ($tipo == 'F') {
                                if (strlen($val) != 11 || !self::validarCPF($val)) {
                                    $errors[$ruleKey] = "O <b>CPF</b> informado é inválido.";
                                }
                            } else {
                                if (strlen($val) != 14 || !self::validarCNPJ($val)) {
                                    $errors[$ruleKey] = "O <b>CNPJ</b> informado é inválido.";
                                }
                            }
                            break;

                        default:
                            break;
                    }
                }
            } else {
                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> é obrigatório [" . $data[$ruleKey] . "].";
            }
        }

        if ($errors) {                          // Se encontrar erros na validação
            Session::set('formErrors', $errors);
            Session::set('formInputs', $data);
            return true;
        } else {
            Session::destroy('formErrors');
            Session::destroy('formInputs');
            return false;
        }
    }

    private static function validarCPF($cpf)
    {
        if (preg_match('/(\d)\1{10}/', $cpf)) return false;
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        return true;
    }

    private static function validarCNPJ($cnpj)
    {
        if (preg_match('/(\d)\1{13}/', $cnpj)) return false;
        $tamanho = strlen($cnpj) - 2;
        $numeros = substr($cnpj, 0, $tamanho);
        $digitos = substr($cnpj, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }
        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
        if ($resultado != $digitos[0]) return false;
        $tamanho = $tamanho + 1;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }
        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
        if ($resultado != $digitos[1]) return false;
        return true;
    }
}
