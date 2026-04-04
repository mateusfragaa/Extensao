<?php

namespace Core\Library;

class Template
{
    private array  $data = [];
    private string $layout = 'default';
    private string $viewPath;
    private string $layoutPath;

    public function __construct()
    {
        $this->viewPath = PATHAPP . 'app' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR;
        $this->layoutPath = $this->viewPath . 'layout' . DIRECTORY_SEPARATOR;
        // '..\app\View\layout\'
    }

    /**
     * setLayout
     *
     * @param string|null $layout
     * @return void
     */
    public function setLayout(?string $layout) : void
    {
        $this->layout = $layout;
    }

    /**
     * assign
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function assign(string $key, mixed $value) : void
    {
        $this->data[$key] = $value;
    }

    /**
     * assignMultiple
     *
     * @param array $data
     * @return void
     */
    public function assignMultiple(array $data) : void
    {
        $this->data = array_merge($this->data, $data);
    }

    public function render(string $view, array $data = [], ?string $layout = null) : void
    {
        if (!empty($data)) {
            $this->assignMultiple($data);
        }

        $safe       = $this->escape($this->data);
        $content    = $this->loadView($view, $safe);

        // sem Layout: Exibe somente o conteúdo da view
        if ($layout === "" || ($layout === null && $this->layout === "")) {
            echo $content;
            return;
        }

        $layoutName = $layout ?? $this->layout;
        $layoutFile = $this->layoutPath . $layoutName . ".php";

        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout não encontrado: {$layoutFile}");
        }

        // Disponibiliza $content e todas as variáveis já escapadas no escopo do layout
        extract($safe, EXTR_SKIP);
        include $layoutFile;
    }

    /**
     * loadView - Carregar a view e retornar o HTML como string
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    private function loadView(string $view, array $data = []) : string
    {
        $viewFile = $this->viewPath . str_replace("/", DIRECTORY_SEPARATOR, $view) . ".php";

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View não encontrada: {$viewFile}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $viewFile;
        return ob_get_clean();
    }

    private function escape(mixed $value) : mixed
    {
        if ($value instanceof Raw) {
            return $value;
        }

        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
        }

        if (is_array($value)) {
            return array_map([$this, 'escape'], $value);
        }

        return $value;
    }
}