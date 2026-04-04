<?php

namespace Core\Library;

class Ambiente
{
    public function load()
    {
        // analisar e carregar o conteúdo do arquivo .env em um array
        // '..\.env'
        $aAmbiente = parse_ini_file(PATHAPP . '.env', true);
   
        // Pega somente as instruções BASEURL=http://fasmicro/
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