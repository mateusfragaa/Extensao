<?php

namespace Core\Library;

trait RequestTrait
{
    static public function getRotaParametros()
    {
        $auxPar         = [];
        $outrosPar      = [];
        $aParametros    = [];

        if (isset($_REQUEST['parametros'])) {
            $auxPar         = filter_var(rtrim($_REQUEST['parametros'], "/"), FILTER_SANITIZE_URL);
            $aParametros    = explode("/", ltrim($auxPar, "/"));
        }
    
        // Outros parâmetros
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