<?php

namespace Core\Library;

class Routes
{
    use RequestTrait;

    /**
     * rota
     *
     * @return void
     */
    public static function rota()
    {
        // Caminho incompleto do controller, falta o nome
        $pathContr      = "App\Controller\\";
        // Possui todos os dados da requisição
        $aParametros    = Self::getRotaParametros();
        // Caminho completo do controller
        $controller     = $pathContr . $aParametros['controller'];
        
        if (!class_exists($controller)) {
            Erros::controllerNotFound($aParametros['controller']);
        } else {
            if (!method_exists($controller, $aParametros['method'])) {
                Erros::methodNotFound($aParametros['method']);
            } else {
                // Instancia do controller criase o objeto
                $instance = new $controller();
                
                // call_user_func_array(NomeFuncao, array_merge($post['a'], $post['b'], $post['c']));
                // call_user_func_array([NomeObjeto,Metodo], array_merge($post['a'], $post['b'], $post['c']));
                call_user_func_array([$instance, $aParametros['method']], array_merge([$aParametros['action'], $aParametros['id']], $aParametros['outrosPar']));

                return;
            }
        }
    }
}