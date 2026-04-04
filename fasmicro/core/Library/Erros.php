<?php

namespace Core\Library;

class Erros
{
    /**
     * controllerNotFound
     *
     * @return void
     */
    public static function controllerNotFound($nomeController = DEFAULT_CONTROLLER)
    {
        echo "Controller ({$nomeController}) não localizado na estrutura do projeto.";
    }

    /**
     * methodNotFound
     *
     * @return void
     */
    public static function methodNotFound($nomeMethod = DEFAULT_METHOD)
    {
        echo "Método ({$nomeMethod}) não localizado no controller.";
    }
}