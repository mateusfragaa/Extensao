<?php

namespace Core\Library;
// Melhor do que herança, interfaces e classe abstratas código facil de ser reutilizados de forma rápida
trait RequestTrait
{
    static public function getRotaParametros()
    { 
        $auxPar         = [];
        $outrosPar      = [];
        $aParametros    = [];

        // $_REQUEST['parametros'] e criado automaticamente pelo .htaccess a cada requisição exceto na primeira
        if (isset($_REQUEST['parametros'])) {
            // rtrim => remove uma barra do final na string
            // rtrim => remove uma barra do começo na string
            $auxPar         = filter_var(rtrim($_REQUEST['parametros'], "/"), FILTER_SANITIZE_URL);
            $aParametros    = explode("/", ltrim($auxPar, "/"));
        }

        // Outros parâmetros
        // URL : [0-Controller/1-Metodo/2-acao/3-Id][/4-Outro/5-Parametro]
        // Todos os parâmetros composição >= 4 são recortados e colocados em outro array
        if (count($aParametros) > 4) {
            $outrosPar = array_slice($aParametros, 4);
        }

        return [
            'controller'    => isset($aParametros[0]) && !empty($aParametros[0]) ? ucfirst($aParametros[0]) : DEFAULT_CONTROLLER,
            'method'        => $aParametros[1] ?? DEFAULT_METHOD,
            'action'        => $aParametros[2] ?? "",
            'id'            => $aParametros[3] ?? 0,
            'outrosPar'     => $outrosPar,
            'get'           => $_GET,
            'post'          => $_POST
        ];
    }
}