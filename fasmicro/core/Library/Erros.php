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
         die(
        '<pre>' .
        $nomeController .
        "\n\n" .
        print_r(debug_backtrace(), true) .
        '</pre>'
    );
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