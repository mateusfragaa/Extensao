<?php

if (!function_exists('_viewData')) {
    function _viewData(?array $data = null): array
    {
        static $store = [];

        if ($data !== null) {
            $store = $data;
        }

        return $store;
    }
}

if (!function_exists('_flashSession')) {
    /**
     * Undocumented function
     * Lê a session na primeira chamada, destroi imediamente (flash) e armazena
     * em m cache estático para as demais chamadas da mesma requisição
     *
     * @param string $key
     * @return array
     */
    function _flashSession(string $key) : array
    {
        static $cache = [];
        
        if (!array_key_exists($key, $cache)) {
            $value = \Core\Library\Session::getDestroy($key);
            $cache[$key] =($value !== false) ? $value : [];
        }

        return $cache[$key];
    }
}

if (!function_exists('setValue')) {
    function setValue(string $key, mixed $default = ''): mixed 
    {
        $segments   = explode(".", $key);
        $formInputs = _flashSession('formInputs');

        // Se a session formInputs existia, usa exclusivamente ela (após a falha da validação)
        if (!empty($formInputs)) {

            $source = $formInputs;

            foreach ($segments as $segment) {
                if (!is_array($source) || !array_key_exists($segment, $source)) {
                    return $default;
                }
                $source = $source[$segment];
            }

            return is_string($source) 
                    ? htmlspecialchars($source, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8")
                    : $source;
        }

        // Sem session, usa os dados do controller (edição de registro existente)
        $source = _viewData()['data'] ?? [];

        foreach (explode(".", $key) as $segment) {
            if (!is_array($source) || !array_key_exists($segment, $source)) {
                return $default;
            }
            $source = $source[$segment];
        }

        return $source ?? $default;
    }
}

if (!function_exists("setMsgFilderError")) {
    function setMsgFilderError(string $campo) : string
    {
        $formErrors = _flashSession('formErrors');

        if (!isset($formErrors[$campo])) {
            return '';
        } 

        return '<div class="mt-2 text-danger">' . $formErrors[$campo] . '</div>';
    }
}

if (!function_exists('nfValor')) {
    function nfValor(float|int|string $valor, int $decimais = 2, string $decimalSep = ',', string $milharSep = '.'): string
    {
        return number_format((float)$valor, $decimais, $decimalSep, $milharSep);
    }
}

if (! function_exists('validateDate')) {
    /**
     * validateDate
     *
     * @param mixed $date 
     * @param string $format 
     * @return bool
     */
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}