<?php

namespace Core\Library;

trait ModelLoaderTrait
{
    /**
     * loadModel
     * Exemplo: loadModel('Produto') → App\Model\ProdutoModel
     *
     * Retorna null se a classe não existir — o chamador decide o que fazer.
     */
    public function loadModel(string $name): ?object
    {
        $class = 'App\\Model\\' . $name . 'Model';

        return class_exists($class) ? new $class() : null;
    }
}
