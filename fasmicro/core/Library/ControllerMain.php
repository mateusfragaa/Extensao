<?php

namespace Core\Library;

class ControllerMain
{
    protected $controller;
    protected $method;
    protected $action;
    protected $request;
    protected $template;

    public $model;

    use RequestTrait;

    /**
     * __construct
     */
    public function __construct()
    {
        // Impedir cache, evitando que ao apertar o voltar do navegador, o usuário volte para dentro do sistema sem se autenticar novamente.
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        $aParametros        = Self::getRotaParametros();
        $this->controller   = $aParametros['controller'];
        $this->method       = $aParametros['method'];
        $this->action       = $aParametros['action'];
        $this->template     = new Template();

        // Carregamento de model default do controller
        $this->model        = $this->loadModel($this->controller);

        // Carregamento de helpers
        $this->loadHelper(['url', 'data']);
        // Verificação de permissão dos controllers autorizados sem login

        $this->checkPermission(); // Chama a trava de segurança

        $this->onConstruct();
    }

    /**
     * loadModel
     *
     * @param string $nomeModel
     * @return void|object
     */
    public function loadModel(string $nomeModel)
    {
        $pathModel = 'App\model\\' . $nomeModel . "Model";

        if (class_exists($pathModel)) {
            return new $pathModel();
        }
    }

    /**
     * view
     * 
     * Exemplo: $this->view("admin/listaProduto", ['titulo' => 'Lista de Produtos'])
     *
     * @param string $view
     * @param array $data
     * @param string|null $layout
     * @return void
     */
    public function view(string $view, array $data = [], ?string $layout = null)
    {
        $this->template->render($view, $data, $layout);
    }

    /**
     * Undocumented function
     *
     * @param string|array $nomeHelper
     * @return void
     */
    public function loadHelper($nomeHelper)
    {
        if (gettype($nomeHelper) == "string") {
            $nomeHelper = [$nomeHelper];
        }

        foreach ($nomeHelper as $value) {
            $pathHelpCore = PATHAPP . "core" . DIRECTORY_SEPARATOR . "Helper" . DIRECTORY_SEPARATOR . "{$value}.php";

            if (file_exists($pathHelpCore)) {
                require_once $pathHelpCore;
            } else {
                $pathHelpApp = PATHAPP . "app" . DIRECTORY_SEPARATOR . "Helper" . DIRECTORY_SEPARATOR . "{$value}.php";

                if (file_exists($pathHelpApp)) {
                    require_once $pathHelpApp;
                }
            }
        }
    }

    private function checkPermission()
    {
        // 1. Tenta criar o admin automaticamente se o banco estiver vazio
        $this->criaSuperUser();

        // 2. Libera acesso às páginas públicas (Home, Login, Auth) definidas no seu Constants.php
        if (in_array($this->controller, CONTROLLER_AUTH)) {
            return;
        }

        // 3. Verifica se o usuário está logado
        $usuarioLogado = \Core\Library\Session::get('usuario_logado');
        if (!$usuarioLogado) {
            header("Location: /auth/formLogin");
            exit;
        }

        // 4. Verifica se a conta ainda está ativa
        if ($usuarioLogado['USU_STATUS'] == 0) {
            \Core\Library\Session::destroy('usuario_logado');
            \Core\Library\Session::set('msgError', 'Sua conta está inativa. Procure o administrador.');
            header("Location: /auth/formLogin");
            exit;
        }

        // 5. Verifica permissão por nível (Admin, Vendedor, etc) usando o array PERMISSOES
        $nivel = $usuarioLogado['USU_NIVEL'];
        $permitidos = PERMISSOES[$nivel] ?? [];

        if (!in_array($this->controller, $permitidos)) {
            \Core\Library\Session::set('msgError', 'Você não tem permissão para acessar esta área!');
            header("Location: /homeSistema");
            exit;
        }
    }

    private function criaSuperUser()
    {
        try {
            // Instanciamos o banco com os parâmetros do .env
            $db = new \Core\Library\Database(
                getenv('DB_CONNECTION') ?: 'mysql',
                getenv('DB_HOST') ?: '127.0.0.1',
                getenv('DB_PORT') ?: '3306',
                getenv('DB_DATABASE') ?: 'extensao',
                getenv('DB_USER') ?: 'root',
                getenv('DB_PASSWORD') ?: ''
            );

            // 1. usamos dbSelect para buscar dados
            $sqlBusca = "SELECT * FROM tb_usuario";
            $stmt = $db->dbSelect($sqlBusca);

            // 2. Usamos dbBuscaArrayAll para transformar o resultado em um array
            $usuarios = $db->dbBuscaArrayAll($stmt);

            if (empty($usuarios)) {
                // 3. SQL de Inserção
                $sqlInsert = "INSERT INTO tb_usuario (USU_NOME, USU_LOGIN, USU_EMAIL, USU_SENHA, USU_NIVEL, USU_STATUS) 
                            VALUES (?, ?, ?, ?, ?, ?)";

                $dados = [
                    'Administrador Geral',
                    'admin',
                    'admin@sistema.com',
                    password_hash('123456', PASSWORD_DEFAULT),
                    'admin',
                    1
                ];

                // 4. Usamos dbInsert para inserir
                $db->dbInsert($sqlInsert, $dados);

                \Core\Library\Session::set('msgSucesso', 'Sistema inicializado! Use login "admin" e senha "123456".');
            }
        } catch (\Exception $e) {
        }
    }

    public function onConstruct()
    {

        // --- Proteção CSRF ---
        if (CSRF_ENABLE && $_SERVER["REQUEST_METHOD"] === "POST") {
            $currentUrl = $_SERVER["REQUEST_URI"];
            $isExcepted = false;
            foreach (CSRF_EXCEPT_URLS as $exceptUrl) {
                if (strpos($currentUrl, $exceptUrl) !== false) {
                    $isExcepted = true;
                    break;
                }
            }

            if (!$isExcepted) {
                if (!\Core\Library\Csrf::validateToken()) {
                    // vamos usar a sessão e voltar
                    \Core\Library\Session::set('msgError', 'Sessão expirada ou token inválido. Por favor, tente novamente.');

                    // Redireciona de volta para onde o usuário estava
                    $referer = $_SERVER['HTTP_REFERER'] ?? '/auth/formLogin';
                    header("Location: " . $referer);
                    exit();
                }
            }
        }
    }

    /**
     * Verifica se o usuário está logado para acessar páginas privadas
     */
    private function checkAuth() {
        // Lista de URLs que podem ser acessadas sem login
        $rotasPublicas = [
            '/auth/formLogin',
            '/auth/login',
            '/auth/logout'
        ];

        // Obtém a URL atual (apenas o caminho, sem parâmetros de busca)
        $urlAtual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Se NÃO estiver logado e a rota NÃO for pública, expulsa para o login
        if (!isset($_SESSION['usuario_logado']) && !in_array($urlAtual, $rotasPublicas)) {
            header("Location: /auth/formLogin");
            exit();
        }
    }
}
