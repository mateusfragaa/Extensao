<?php

namespace Core\Library;

use PDO;

class Validator
{
    /**
     * Validação pura: retorna array de erros ou null se tudo válido.
     * Não toca na sessão — pode ser usada por qualquer camada (web, API, CLI).
     *
     * Regras suportadas (separadas por |):
     *   required         Campo obrigatório
     *   nullable         Campo pode ser nulo/vazio — ignora demais regras se vazio
     *   sometimes        Só valida se o campo estiver presente no array $data
     *   email            E-mail válido
     *   float            Número decimal
     *   int              Número inteiro
     *   min:N            Mínimo de N caracteres (string) ou N itens (array)
     *   max:N            Máximo de N caracteres (string) ou N itens (array)
     *   confirmed        Deve corresponder ao campo {field}_confirmation
     *   date             Data válida (strtotime)
     *   after:date       Data posterior a 'date' (aceita 'today')
     *   before:date      Data anterior a 'date' (aceita 'today')
     *   regex:/pattern/  Deve corresponder à expressão regular
     *   in:a,b,c         Valor deve estar na lista
     *   not_in:a,b,c     Valor não deve estar na lista
     *   url              URL válida
     *   cpf              CPF válido (com ou sem formatação)
     *   array            Deve ser um array
     *   unique:tbl,col[,exceptId,idField]  Unicidade no banco de dados
     *   mimes:jpg,png    Tipo do arquivo (chave em $_FILES deve ser igual ao campo)
     *   max_file:KB      Tamanho máximo do arquivo em KB
     *
     * @param array $data  Dados a validar
     * @param array $rules Regras no formato ['campo' => ['label' => '...', 'rules' => '...']]
     * @return array<string, string>|null
     */
    public static function check(array $data, array $rules): ?array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $label = $rule['label'] ?? $field;
            $itens = explode('|', $rule['rules'] ?? '');
            $value = $data[$field] ?? null;

            // sometimes: pula completamente se o campo não existe no array
            if (in_array('sometimes', $itens, true) && !array_key_exists($field, $data)) {
                continue;
            }

            $isEmpty = ($value === null || $value === '' || (is_array($value) && count($value) === 0));

            // nullable: campo vazio é permitido, ignora demais regras
            if ($isEmpty && in_array('nullable', $itens, true)) {
                continue;
            }

            // Campo vazio sem nullable
            if ($isEmpty) {
                if (in_array('required', $itens, true)) {
                    $errors[$field] = "O campo \"$label\" é obrigatório.";
                }
                continue;
            }

            foreach ($itens as $item) {
                [$ruleName, $ruleParam] = array_pad(explode(':', $item, 2), 2, null);

                switch ($ruleName) {

                    case 'required':
                        if ((string)$value === '') {
                            $errors[$field] = "O campo \"$label\" deve ser preenchido.";
                        }
                        break;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "O campo \"$label\" não é um e-mail válido.";
                        }
                        break;

                    case 'float':
                        if (!filter_var(str_replace(',', '.', (string)$value), FILTER_VALIDATE_FLOAT)) {
                            $errors[$field] = "O campo \"$label\" deve conter um número decimal.";
                        }
                        break;

                    case 'int':
                        if (!filter_var($value, FILTER_VALIDATE_INT)) {
                            $errors[$field] = "O campo \"$label\" deve conter um número inteiro.";
                        }
                        break;

                    case 'min':
                        if (is_array($value)) {
                            if (count($value) < (int)$ruleParam) {
                                $errors[$field] = "O campo \"$label\" deve ter no mínimo {$ruleParam} item(ns).";
                            }
                        } elseif (strlen(strip_tags((string)$value)) < (int)$ruleParam) {
                            $errors[$field] = "O campo \"$label\" deve ter no mínimo {$ruleParam} caracteres.";
                        }
                        break;

                    case 'max':
                        if (is_array($value)) {
                            if (count($value) > (int)$ruleParam) {
                                $errors[$field] = "O campo \"$label\" deve ter no máximo {$ruleParam} item(ns).";
                            }
                        } elseif (strlen(strip_tags((string)$value)) > (int)$ruleParam) {
                            $errors[$field] = "O campo \"$label\" deve ter no máximo {$ruleParam} caracteres.";
                        }
                        break;

                    case 'confirmed':
                        $confirmKey = $field . '_confirmation';
                        if (!isset($data[$confirmKey]) || $data[$confirmKey] !== $value) {
                            $errors[$field] = "O campo \"$label\" não confere com a confirmação.";
                        }
                        break;

                    case 'date':
                        if (strtotime((string)$value) === false) {
                            $errors[$field] = "O campo \"$label\" não é uma data válida.";
                        }
                        break;

                    case 'after':
                        $ref = $ruleParam === 'today' ? date('Y-m-d') : $ruleParam;
                        if (strtotime((string)$value) === false || strtotime((string)$value) <= strtotime($ref)) {
                            $label_ref = $ruleParam === 'today' ? 'hoje' : $ruleParam;
                            $errors[$field] = "O campo \"$label\" deve ser uma data posterior a {$label_ref}.";
                        }
                        break;

                    case 'before':
                        $ref = $ruleParam === 'today' ? date('Y-m-d') : $ruleParam;
                        if (strtotime((string)$value) === false || strtotime((string)$value) >= strtotime($ref)) {
                            $label_ref = $ruleParam === 'today' ? 'hoje' : $ruleParam;
                            $errors[$field] = "O campo \"$label\" deve ser uma data anterior a {$label_ref}.";
                        }
                        break;

                    case 'regex':
                        if (@preg_match($ruleParam, (string)$value) !== 1) {
                            $errors[$field] = "O campo \"$label\" possui formato inválido.";
                        }
                        break;

                    case 'in':
                        $allowed = array_map('trim', explode(',', $ruleParam ?? ''));
                        if (!in_array((string)$value, $allowed, true)) {
                            $errors[$field] = "O valor do campo \"$label\" não é permitido.";
                        }
                        break;

                    case 'not_in':
                        $forbidden = array_map('trim', explode(',', $ruleParam ?? ''));
                        if (in_array((string)$value, $forbidden, true)) {
                            $errors[$field] = "O valor do campo \"$label\" não é permitido.";
                        }
                        break;

                    case 'url':
                        if (!filter_var($value, FILTER_VALIDATE_URL)) {
                            $errors[$field] = "O campo \"$label\" não é uma URL válida.";
                        }
                        break;

                    case 'cpf':
                        if (!self::validarCpf((string)$value)) {
                            $errors[$field] = "O campo \"$label\" não é um CPF válido.";
                        }
                        break;

                    case 'array':
                        if (!is_array($value)) {
                            $errors[$field] = "O campo \"$label\" deve ser um array.";
                        }
                        break;

                    case 'unique':
                        // unique:tabela,coluna[,exceptId,campoId]
                        $parts    = array_map('trim', explode(',', $ruleParam ?? ''));
                        $table    = $parts[0] ?? null;
                        $column   = $parts[1] ?? null;
                        $exceptId = $parts[2] ?? null;
                        $idField  = $parts[3] ?? 'id';

                        if ($table && $column) {
                            $uniqueError = self::checkUnique($table, $column, $value, $exceptId, $idField);
                            if ($uniqueError) {
                                $errors[$field] = "O valor do campo \"$label\" já está em uso.";
                            }
                        }
                        break;

                    case 'mimes':
                        // Valida o MIME type de um arquivo em $_FILES[$field]
                        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                            $allowed  = array_map('trim', explode(',', $ruleParam ?? ''));
                            $mimeMap  = self::extensionToMime();
                            $allowed_mimes = [];
                            foreach ($allowed as $ext) {
                                if (isset($mimeMap[$ext])) {
                                    $allowed_mimes = array_merge($allowed_mimes, (array)$mimeMap[$ext]);
                                }
                            }
                            $fileMime = mime_content_type($_FILES[$field]['tmp_name']);
                            if (!in_array($fileMime, $allowed_mimes, true)) {
                                $ext_list = implode(', ', $allowed);
                                $errors[$field] = "O campo \"$label\" aceita apenas: {$ext_list}.";
                            }
                        }
                        break;

                    case 'max_file':
                        // Valida o tamanho máximo de um arquivo em $_FILES[$field] (em KB)
                        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                            $maxKb    = (int)$ruleParam;
                            $fileSize = (int)$_FILES[$field]['size'];
                            if ($fileSize > $maxKb * 1024) {
                                $errors[$field] = "O arquivo do campo \"$label\" excede o limite de {$maxKb} KB.";
                            }
                        }
                        break;
                }

                if (isset($errors[$field])) {
                    break;
                }
            }
        }

        return empty($errors) ? null : $errors;
    }

    /**
     * make
     *
     * @return bool true se houver erros, false se válido
     */
    public static function make(array $data, array $rules): bool
    {
        $errors = self::check($data, $rules);

        if ($errors) {
            // Converte aspas duplas em torno do label para <b> nas mensagens HTML
            $htmlErrors = array_map(
                fn($msg) => preg_replace('/"([^"]+)"/', '<b>$1</b>', $msg),
                $errors
            );

            Session::set('formErrors', $htmlErrors);
            Session::set('formInputs', $data);
            return true;
        }

        Session::destroy('formErrors');
        Session::destroy('formInputs');
        return false;
    }

    /**
     * validarCpf - Valida um CPF brasileiro (com ou sem formatação).
     *
     * @param string $cpf
     * @return bool
     */
    private static function validarCpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int)$cpf[$i] * ($t + 1 - $i);
            }
            $resto = ($soma * 10) % 11;
            if ($resto === 10 || $resto === 11) {
                $resto = 0;
            }
            if ($resto !== (int)$cpf[$t]) {
                return false;
            }
        }

        return true;
    }

    /**
     * checkUnique - Verifica unicidade no banco de dados.
     *
     * @param string      $table
     * @param string      $column
     * @param mixed       $value
     * @param string|null $exceptId   ID a ignorar (para edições)
     * @param string      $idField    Nome do campo de ID da tabela
     * @return bool  true = já existe (erro), false = único (ok)
     */
    private static function checkUnique(string $table, string $column, mixed $value, ?string $exceptId, string $idField): bool
    {
        try {
            $dsn = match ($_ENV['DB_CONNECTION'] ?? 'mysql') {
                'sqlsrv' => "sqlsrv:Server={$_ENV['DB_HOST']},{$_ENV['DB_PORT']};DataBase={$_ENV['DB_DATABASE']}",
                default  => "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}",
            };

            $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql    = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
            $params = [$value];

            if ($exceptId !== null && $exceptId !== '') {
                $sql    .= " AND {$idField} <> ?";
                $params[] = $exceptId;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            return (int)$stmt->fetchColumn() > 0;

        } catch (\Exception) {
            return false;
        }
    }

    /**
     * extensionToMime - Mapa de extensões comuns para MIME types.
     *
     * @return array<string, string|string[]>
     */
    private static function extensionToMime(): array
    {
        return [
            'jpg'  => ['image/jpeg', 'image/jpg'],
            'jpeg' => ['image/jpeg', 'image/jpg'],
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'bmp'  => 'image/bmp',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt'  => 'text/plain',
            'csv'  => 'text/csv',
            'zip'  => 'application/zip',
            'mp3'  => 'audio/mpeg',
            'mp4'  => 'video/mp4',
            'json' => 'application/json',
            'xml'  => 'application/xml',
        ];
    }
}
