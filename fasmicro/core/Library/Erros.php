<?php

namespace Core\Library;

class Erros
{
    /**
     * controllerNotFound - Responde com 404 ao tentar acessar controller inexistente.
     *
     * @param string $nomeController
     * @return void
     */
    public static function controllerNotFound(string $nomeController = DEFAULT_CONTROLLER): void
    {
        ErrorHandler::handleException(
            new \RuntimeException("Controller «{$nomeController}» não localizado.", 404)
        );
    }

    /**
     * methodNotFound - Responde com 404 ao tentar acessar método inexistente.
     *
     * @param string $nomeMethod
     * @return void
     */
    public static function methodNotFound(string $nomeMethod = DEFAULT_METHOD): void
    {
        ErrorHandler::handleException(
            new \RuntimeException("Método «{$nomeMethod}» não localizado no controller.", 404)
        );
    }
}