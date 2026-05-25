<?php

namespace Core\Library;

class Request
{
    protected $param;

    use RequestTrait;

    public function __construct()
    {
        $this->param = Self::getRotaParametros();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getController()
    {
        return $this->param['controller'];
    }

    public function getPost(): array
    {
        return array_map('trim', $this->param['post']);
    }

    public function getAction()
    {
        return $this->param['action'];
    }

    public function getRequestId()
    {
        return $this->param['id'];
    }
}
