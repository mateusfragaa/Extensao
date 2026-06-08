<?php

namespace Core\Library;

class Ambiente
{
    /**
     * get
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * load
     *
     * @return void
     */
    public function load()
    {
        // analisar e carregar o conteúdo do arquivo .env em um array
        $aAmbiente = parse_ini_file(PATHAPP . '.env', true);

        foreach ($aAmbiente as $key => $value) {
            if (gettype($aAmbiente[$key]) != "array") {
                $_ENV[$key] = $value;
            }
        }

        // pegar as configurações de ambinte da base de dados
        if (isset($_ENV['ENVIRIONMENT'])) {
            foreach ($aAmbiente[$_ENV['ENVIRIONMENT']] as $key => $value) {
                $_ENV[$key] = $value;
            }
        }

        return null;
    }
}