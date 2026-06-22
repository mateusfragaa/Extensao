# Arquitetura do Projeto

## Estrutura de Diretórios

```
fasmicro/
├── app/                        # Código da aplicação
│   ├── Config/
│   │   ├── Constants.php       # Constantes globais (timezone, versão da API, etc.)
│   │   └── ApiRoutesConfig.php # Registro de rotas da API REST
│   ├── Controller/
│   │   ├── Home.php            # Página inicial e formulário de contato
│   │   ├── Login.php           # Autenticação (entrar / sair)
│   │   ├── Produto.php         # CRUD de produtos
│   │   ├── Categoria.php       # CRUD de categorias
│   │   ├── UnidadeMedida.php   # CRUD de unidades de medida
│   │   └── Api/
│   │       ├── ApiControllerMain.php  # Classe base para controllers de API
│   │       ├── AuthApiController.php  # Login, refresh token, me
│   │       └── ProdutoApi.php         # CRUD REST de produtos
│   ├── Helper/
│   │   ├── formHelper.php      # Renderização de campos de formulário
│   │   └── jsHelper.php        # Utilitários JavaScript
│   ├── Model/
│   │   ├── ProdutoModel.php
│   │   ├── CategoriaModel.php
│   │   ├── UnidadeMedidaModel.php
│   │   └── UsuarioModel.php
│   └── View/
│       ├── admin/              # Views da área administrativa
│       ├── Layout/             # Layouts reutilizáveis (default, login)
│       ├── login/              # View de login
│       ├── home.php            # Página inicial pública
│       └── contato.php         # Formulário de contato
│
├── core/                       # Núcleo do framework
│   ├── Helper/
│   │   ├── data.php            # Formatação de datas e dados
│   │   └── url.php             # Geração de URLs
│   └── Library/
│       ├── Ambiente.php        # Leitura do .env
│       ├── ApiAuth.php         # Validação e extração de token JWT
│       ├── ApiResponse.php     # Respostas JSON padronizadas
│       ├── ApiRoutes.php       # Motor de roteamento REST
│       ├── ControllerMain.php  # Controller base (web)
│       ├── Csrf.php            # Proteção CSRF (Synchronizer Token Pattern)
│       ├── Database.php        # Abstração PDO (query builder)
│       ├── Email.php           # Wrapper PHPMailer
│       ├── ErrorHandler.php    # Handler global de exceções e erros PHP
│       ├── Erros.php           # Respostas 404/500 para controller/método não encontrado
│       ├── Logger.php          # Sistema de logging estruturado compatível com PSR-3
│       ├── Files.php           # Upload de arquivos
│       ├── Jwt.php             # Tokens JWT (HS256, sem dependências externas)
│       ├── ModelLoaderTrait.php# Carregamento dinâmico de Models
│       ├── ModelMain.php       # Model base com CRUD e validação
│       ├── Raw.php             # Saída de dados brutos
│       ├── Redirect.php        # Redirecionamentos com mensagens flash
│       ├── Request.php         # Dados da requisição HTTP
│       ├── RequestTrait.php    # Trait auxiliar de requisição
│       ├── Routes.php          # Roteador URL → Controller/Método
│       ├── Session.php         # Gerenciamento de sessão
│       ├── Template.php        # Motor de templates/views
│       └── Validator.php       # Validação de formulários
│
├── public/                     # Document root — único diretório público
│   ├── assests/
│   │   ├── bootstrap/          # CSS e JS do Bootstrap 5
│   │   ├── css/                # Estilos customizados
│   │   ├── img/                # Imagens públicas
│   │   └── js/                 # Scripts customizados
│   ├── .htaccess               # Rewrite rules (tudo para index.php)
│   └── index.php               # Front controller
│
├── storage/
│   └── logs/                   # Logs diários gerados pelo Logger (app-YYYY-MM-DD.log)
├── uploads/                    # Arquivos enviados pelos usuários
├── vendor/                     # Dependências Composer (PHPMailer)
├── docs/                       # Documentação
├── .env                        # Configurações do ambiente (não versionado)
├── exemplo.env                 # Template do .env
├── composer.json
└── script-database.sql         # Script de criação do banco de dados
```

---

## Padrão MVC

O FasMicro segue o padrão **Model-View-Controller**:

- **Model** (`app/Model/`) — lógica de negócio e acesso ao banco via `ModelMain`
- **View** (`app/View/`) — templates PHP com layouts reutilizáveis gerenciados por `Template`
- **Controller** (`app/Controller/`) — orquestração entre Model e View; controllers de API herdam de `ApiControllerMain`

---

## Classes do Núcleo (`core/Library/`)

| Classe | Responsabilidade |
|---|---|
| `Ambiente` | Leitura e parse do arquivo `.env` |
| `Routes` | Roteador web: URL → Controller/Método |
| `ApiRoutes` | Roteador REST com suporte a parâmetros dinâmicos |
| `ControllerMain` | Classe base para controllers web — inclui validação CSRF automática |
| `Csrf` | Proteção CSRF: geração, armazenamento em sessão e validação de token |
| `ErrorHandler` | Handler global: captura exceções, erros PHP e fatais; responde em HTML ou JSON (API); loga via `Logger` |
| `Logger` | Logging estruturado (PSR-3): oito níveis, rotação diária em `storage/logs/`, filtragem por `LOG_LEVEL` |
| `ModelMain` | CRUD genérico + validação via PDO |
| `Database` | Abstração PDO com query builder e suporte a transações |
| `Template` | Motor de templates com escape automático de HTML |
| `Request` | Acesso aos dados da requisição HTTP (GET, POST, JSON) — filtra o token CSRF do POST |
| `Session` | Gerenciamento de sessão PHP |
| `Redirect` | Redirecionamentos com flash messages |
| `Validator` | Validação de formulários com regras configuráveis por model |
| `Jwt` | Geração e verificação de tokens JWT (HS256) |
| `ApiAuth` | Middleware de autenticação JWT para a API |
| `ApiResponse` | Respostas JSON padronizadas |
| `Email` | Wrapper PHPMailer para envio de e-mail via SMTP |
| `Files` | Upload, validação e armazenamento de arquivos |
| `ModelLoaderTrait` | Carregamento dinâmico de Models nos Controllers |
