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
     * getController
     *
     * @return string
     */
    public function getController()
    {
        return $this->param['controller'];
    }

    /**
     * getMetodo
     *
     * @return string
     */
    public function getMetodo()
    {
        return $this->param['method'];
    }

    /**
     * getAction
     *
     * @return string
     */
    public function getAction() 
    {
        return $this->param['action'];
    }

    /**
     * getGet
     * 
     * @return array<string, mixed>
     */
    public function getGet(): array
    {
        return $this->trimRecursive($this->param['get']);
    }

    /**
     * getPost
     *
     * @return array<string, mixed>
     */
    public function getPost(): array
    {
        $post = $this->param['post'];
        unset($post[CSRF_TOKEN_NAME]);
        return $this->trimRecursive($post);
    }

    /**
     * trimRecursive
     *
     * @param array $data
     * @return array
     */
    private function trimRecursive(array $data): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return $this->trimRecursive($value);
            }
            return is_string($value) ? trim($value) : $value;
        }, $data);
    }

    /**
     * formAction
     *
     * @return string
     */
    public function formAction()
    {
        return baseUrl() . $this->getController() . '/' . $this->getAction();
    }

    /**
     * getHttpMethod
     * 
     * Retorna o método HTTP real da requisição (GET, POST, PUT, PATCH, DELETE).
     */
    public function getHttpMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * getBody
     * 
     * Retorna o body da requisição decodificado como array.
     * Suporta Content-Type application/json e application/x-www-form-urlencoded.
     *
     * @return array<string, mixed>
     */
    public function getBody(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $raw  = file_get_contents('php://input');
            $data = json_decode($raw, true);
            return is_array($data) ? $this->trimRecursive($data) : [];
        }

        // form-urlencoded ou multipart (PUT/PATCH não populam $_POST nativamente)
        if (in_array($this->getHttpMethod(), ['PUT', 'PATCH'], true) && empty($_POST)) {
            parse_str(file_get_contents('php://input'), $data);
            return $this->trimRecursive($data);
        }

        return $this->getPost();
    }

    /**
     * getHeader
     * 
     * Retorna um header HTTP pelo nome (case-insensitive).
     * Exemplo: getHeader('Authorization') → 'Bearer eyJ...'
     */
    public function getHeader(string $name): string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$key] ?? '';
    }
}