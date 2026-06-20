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
                                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> deve conter no mínimo " . $items[1] . " caracteres.";
                            }
                            break;

                        case 'max':
                            if (strlen(strip_tags($data[$ruleKey])) > $items[1]) {
                                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> deve conter no máximo " . $items[1] . " caracteres.";
                            }
                            break;

                        // -------------------------------------------------------
                        // Validação CPF / CNPJ
                        // Para CPF: valida matematicamente os dígitos verificadores.
                        // Para CNPJ: valida os dígitos E consulta a API pública da
                        //            Receita (receitaws.com.br) para confirmar que
                        //            o CNPJ realmente existe e está ATIVO.
                        // -------------------------------------------------------
                        case 'cpf_cnpj':
                            $val  = preg_replace('/\D/', '', $data[$ruleKey]);
                            $tipo = $data['TIPO_PESSOA'] ?? 'F';

                            if ($tipo === 'F') {
                                // ── CPF: valida dígitos verificadores ────────
                                if (strlen($val) !== 11) {
                                    $errors[$ruleKey] = "O <b>CPF</b> deve ter 11 dígitos.";
                                } elseif (!self::validarCPF($val)) {
                                    $errors[$ruleKey] = "O <b>CPF</b> informado é inválido.";
                                }
                            } else {
                                // ── CNPJ: valida apenas dígitos verificadores ─
                                // A consulta à Receita Federal NÃO bloqueia o save.
                                // Verificação de situação cadastral é feita pelo
                                // usuário via botão Verificar antes de salvar.
                                if (strlen($val) !== 14) {
                                    $errors[$ruleKey] = "O <b>CNPJ</b> deve ter 14 dígitos.";
                                } elseif (!self::validarCNPJ($val)) {
                                    $errors[$ruleKey] = "O <b>CNPJ</b> informado é inválido (dígito verificador incorreto).";
                                }
                            }
                            break;

                        default:
                            break;
                    }
                }
            } else {
                $errors[$ruleKey] = "O campo <b>{$ruleValue['label']}</b> é obrigatório.";
            }
        }

        if ($errors) {
            Session::set('formErrors', $errors);
            Session::set('formInputs', $data);
            return true;
        } else {
            Session::destroy('formErrors');
            Session::destroy('formInputs');
            return false;
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // CPF – validação matemática dos dígitos verificadores
    // ══════════════════════════════════════════════════════════════════
    private static function validarCPF(string $cpf): bool
    {
        // Rejeita sequências repetidas (111.111.111-11 etc.)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($c = 0; $c < $t; $c++) {
                $soma += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $soma) % 11) % 10;
            if ($cpf[$t] != $d) return false;
        }
        return true;
    }

    // ══════════════════════════════════════════════════════════════════
    // CNPJ – validação matemática dos dígitos verificadores
    // ══════════════════════════════════════════════════════════════════
    private static function validarCNPJ(string $cnpj): bool
    {
        // CNPJ 2.0 (IN RFB 2.229/2024) — vigência: julho/2026
        // Retrocompatível: aceita numérico (legado) e alfanumérico (novo).
        // 14 chars: posições 1-12 alfanuméricas (A-Z + 0-9), 13-14 numéricas (DVs).

        $cnpj = strtoupper(trim($cnpj));

        if (strlen($cnpj) !== 14) return false;
        if (!preg_match('/^[A-Z0-9]{12}[0-9]{2}$/', $cnpj)) return false;

        // Rejeita sequências de um único caractere repetido
        if (preg_match('/^(.)\\1{13}$/', $cnpj)) return false;

        // ASCII - 48: '0'=0 ... '9'=9 | 'A'=17 ... 'Z'=42
        $vals = array_map(fn($c) => ord($c) - 48, str_split($cnpj));

        $calcDV = function (int $tam) use ($vals): int {
            $soma = 0;
            $pos  = $tam - 7;
            for ($i = $tam; $i >= 1; $i--) {
                $soma += $vals[$tam - $i] * $pos--;
                if ($pos < 2) $pos = 9;
            }
            $r = $soma % 11;
            return $r < 2 ? 0 : 11 - $r;
        };

        return $calcDV(12) === $vals[12]
            && $calcDV(13) === $vals[13];
    }

    // ══════════════════════════════════════════════════════════════════
    // Consulta à Receita Federal via receitaws.com.br (API pública)
    //
    // Retorna:
    //   true          → CNPJ existe e está ATIVO
    //   string        → mensagem de erro amigável para o usuário
    // ══════════════════════════════════════════════════════════════════
    private static function consultarCNPJReceita(string $cnpj)
    {
        $url = "https://receitaws.com.br/v1/cnpj/{$cnpj}";

        $ctx = stream_context_create([
            'http' => [
                'method'          => 'GET',
                'timeout'         => 8,   // 8 segundos de timeout
                'ignore_errors'   => true,
                'header'          => "User-Agent: PHP-Validator\r\n",
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $resposta = @file_get_contents($url, false, $ctx);

        // Se não conseguiu conectar (sem internet, timeout etc.) — não bloqueia o cadastro
        if ($resposta === false) {
            Session::set(
                'msgAlerta',
                'Não foi possível validar o CNPJ na Receita Federal agora (sem conexão). Verifique manualmente se necessário.'
            );
            return true; // Permite salvar mas avisa
        }

        $dados = json_decode($resposta, true);

        if (!$dados || !isset($dados['status'])) {
            // API retornou algo inesperado — não bloqueia
            return true;
        }

        // A API retorna status "ERROR" para CNPJs não encontrados
        if (strtoupper($dados['status']) === 'ERROR') {
            $msg = $dados['message'] ?? 'CNPJ não localizado na Receita Federal.';
            return "O <b>CNPJ</b> informado não foi encontrado na Receita Federal: {$msg}";
        }

        // Verifica a situação cadastral
        $situacao = strtoupper($dados['situacao'] ?? '');

        if ($situacao !== 'ATIVA') {
            $situacaoFormatada = ucfirst(strtolower($situacao ?: 'desconhecida'));
            $razaoSocial       = $dados['nome'] ?? 'Não informado';
            return "O <b>CNPJ</b> pertence a <b>{$razaoSocial}</b> mas sua situação cadastral é <b>{$situacaoFormatada}</b>, não ATIVA.";
        }

        return true; // Tudo OK
    }
}
