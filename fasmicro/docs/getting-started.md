# FasMicro — Documentação

Bem-vindo à documentação do framework **FasMicro**.

## Índice

| Documento | Descrição |
|-----------|-----------|
| [Instalação e Configuração](#instalação) | Pré-requisitos, passos de setup e variáveis de ambiente |
| [Arquitetura](architecture.md) | Estrutura de diretórios e classes do núcleo |
| [Rotas Web](routes.md) | Rotas públicas e administrativas da aplicação |
| [Banco de Dados](database.md) | Tabelas, relacionamentos e papéis de usuário |
| [Segurança — CSRF](security.md) | Proteção CSRF: como funciona, configuração e uso em formulários/AJAX |
| [Logging (PSR-3)](logging.md) | Logger: níveis, configuração, uso nos controllers e integração com ErrorHandler |

### API REST

| Documento | Descrição |
|-----------|-----------|
| [Visão Geral](api/overview.md) | URL base, CORS e variáveis de ambiente |
| [Autenticação](api/authentication.md) | JWT, login, refresh token e níveis de acesso |
| [Endpoints](api/endpoints.md) | Referência completa com exemplos de request/response |
| [Formato de Respostas](api/response-format.md) | Estrutura padrão de sucesso, erro e paginação |

---

## Pré-requisitos

- PHP 7.4 ou superior
- MySQL 8.x
- Apache com `mod_rewrite` habilitado
- Composer

---

## Instalação

**1. Clone o repositório**

```bash
git clone <url-do-repositorio>
cd fasmicro
```

**2. Instale as dependências**

```bash
composer install
```

**3. Configure o ambiente**

Copie o arquivo de exemplo e preencha com suas credenciais:

```bash
cp exemplo.env .env
```

**4. Crie o banco de dados**

```bash
mysql -u root -p < script-database.sql
```

**5. Configure o Apache**

Aponte o `DocumentRoot` do virtual host para a pasta `public/`:

```apache
DocumentRoot "/caminho/para/fasmicro/public"
```

Certifique-se de que `mod_rewrite` está ativo e que `AllowOverride All` está configurado no diretório.

---

## Configuração do `.env`

```ini
; URL base do framework
BASEURL=http://fasmicro/

; Conta para disparo de e-mail
MAIL.HOST=smtp.gmail.com
MAIL.SMTPAuth=true
MAIL.PORT=587
MAIL.SMTPSECURE=tls
MAIL.NOME=seu_nome
MAIL.USER=seu_email@seu_dominio.com
MAIL.SENHA=sua_senha

; Logging (PSR-3) — níveis: debug | info | notice | warning | error | critical | alert | emergency
; Em produção use "error" para registrar apenas falhas relevantes
LOG_LEVEL=debug

; API JWT — OBRIGATÓRIO para usar a API REST
JWT_SECRET=troque_esta_chave_por_uma_string_longa_e_aleatoria
JWT_EXPIRE=3600
JWT_REFRESH_EXPIRE=604800

; CORS — domínio permitido (* = qualquer origem; em produção, use o domínio real)
API_CORS_ORIGIN=*

; Super usuário inicial
SUPERUSER_EMAIL=seu_email@seudominio.com.br
SUPERUSER_SENHA=sua_senha
SUPERUSER_NOME=seu_nome

; Ambiente ativo
ENVIRIONMENT=DEVELOPMENT

[DEVELOPMENT]
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fasmicro
DB_USER=root
DB_PASSWORD=

[PRODUCTION]
DB_CONNECTION=mysql
DB_HOST=seu_servidor
DB_PORT=3306
DB_DATABASE=sua_base
DB_USER=seu_user
DB_PASSWORD=sua_senha
```

> **Nota:** As constantes de CSRF são definidas diretamente em `app/Config/Constants.php`
> com valores padrão seguros. Consulte [docs/security.md](security.md) para detalhes.

---

## Adicionando novos recursos à API

1. Crie o controller em `app/Controller/Api/` estendendo `ApiControllerMain`
2. Registre as rotas em `app/Config/ApiRoutesConfig.php` usando `ApiRoutes::get/post/put/patch/delete`
3. Documente o novo recurso em `docs/api/endpoints.md`
