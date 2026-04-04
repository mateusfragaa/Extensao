<?php

namespace Core\Library;

class Raw
{
    public function __construct(private readonly string $value) {}

    /**
     * __toString
     * 
     * Wrapper para valores que não devem ser escapados pelo Template.
     * Use apenas para HTML confiavél (ex: saída de editores), já sanitizados, ou
     * HTML gerado internamente pela aplicação.
     * 
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->value;
    }
}