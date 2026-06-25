<?php

namespace Core\Library;

use Core\Library\Csrf;
use Core\Library\Redirect;
use Core\Library\Request;

class ControllerMain
{
    protected $controller;
    protected $method;
    protected $action;
    protected $request;
    protected $template;

    public $model;

    use RequestTrait;
    use ModelLoaderTrait;   // ← padrão do professor (loadModel extraído para trait)

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
        $this->request      = new Request();

        // Validação CSRF: rejeita requisições mutantes sem token válido
        if (CSRF_PROTECTION && !Csrf::isExcluded()) {
            $httpMethod = $this->request->getHttpMethod();
            if (in_array($httpMethod, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $headerVar = 'HTTP_' . strtoupper(str_replace('-', '_', CSRF_HEADER_NAME));
                $token     = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER[$headerVar] ?? null;
                if (!Csrf::validate($token)) {
                    http_response_code(419);
                    return Redirect::page('Login/', ['msgError' => 'Token de segurança inválido. Recarregue a página e tente novamente.']);
                }
            }
        }

        // Verificação de permissão dos controllers autorizados sem login

        $this->checkPermission(); // Chama a trava de segurança

        $this->onConstruct();

        // Carregamento de helpers globais (padrão do professor)
        $this->loadHelper(['url', 'data', 'formHelper']);

        // Carregamento do model default do controller
        $this->model = $this->loadModel($this->controller);

        // Verificação de autenticação
        // NOTA: O login do projeto de extensão ainda não implementa sessão userId.
        // Quando o grupo implementar a autenticação real, basta remover o Auth
        // e HomeSistema da lista CONTROLLER_AUTH em Constants.php.
        if (!in_array($this->controller, CONTROLLER_AUTH)) {
            if (!Session::get('userId')) {
                return Redirect::page('Login/', ['msgError' => 'Para acessar o sistema, faça login primeiro.']);
            }
        }
    }

    /**
     * validaNivelAcesso
     * Redireciona se o nível do usuário logado for maior que o mínimo exigido.
     * Níveis: 1 = Super Admin, 11 = Admin, 21 = Usuário comum.
     *
     * @param int $nivelMinimo
     */
    public function validaNivelAcesso(int $nivelMinimo = 20): void
    {
        if ((int) Session::get('userNivel') > $nivelMinimo) {
            Redirect::page('Login/', ['msgError' => 'Você não possui permissão para acessar esta funcionalidade.']);
        }
    }

    /**
     * loadService
     *
     * @param string $nomeService
     * @return void|object
     */
    public function loadService(string $nomeService)
    {
        $pathService = 'App\Service\\' . $nomeService . "Service";

        if (class_exists($pathService)) {
            return new $pathService();
        }
    }

    /**
     * view
     */
    public function view(string $view, array $data = [], ?string $layout = null)
    {
        $this->template->render($view, $data, $layout);
    }

    /**
     * loadHelper
     */
    public function loadHelper($nomeHelper)
    {
        if (gettype($nomeHelper) == 'string') {
            $nomeHelper = [$nomeHelper];
        }

        foreach ($nomeHelper as $value) {
            $pathHelpCore = PATHAPP . "core" . DIRECTORY_SEPARATOR . "Helper" . DIRECTORY_SEPARATOR . "{$value}.php";
            if (file_exists($pathHelpCore)) {
                require_once $pathHelpCore;
            } else {
                $pathHelpApp = PATHAPP . 'app' . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR . "{$value}.php";
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
        if (CSRF_PROTECTION && $_SERVER["REQUEST_METHOD"] === "POST") {
            $currentUrl = $_SERVER["REQUEST_URI"];
            $isExcepted = false;
            foreach (CSRF_EXCLUDE_URIS as $exceptUrl) {
                if (strpos($currentUrl, $exceptUrl) !== false) {
                    $isExcepted = true;
                    break;
                }
            }

            if (!$isExcepted) {
                if (!\Core\Library\Csrf::validate($_POST[\CSRF_TOKEN_NAME] ?? null)) {
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
