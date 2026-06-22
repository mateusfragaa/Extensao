# Segurança — Proteção CSRF

## O que é CSRF?

**Cross-Site Request Forgery (CSRF)** é um ataque em que um site malicioso engana o navegador de um usuário autenticado para que ele envie requisições involuntárias a outro site onde já está logado.

**Exemplo de ataque:**

```
1. Usuário faz login no FasMicro e recebe um cookie de sessão.
2. Sem sair, o usuário visita um site malicioso.
3. Esse site contém um formulário oculto apontando para /Produto/delete.
4. O formulário é enviado automaticamente — o navegador inclui o cookie da sessão.
5. O servidor, sem proteção CSRF, executa a exclusão como se fosse o próprio usuário.
```

> **Rotas de API não são afetadas:** as rotas `/api/*` usam autenticação via header `Authorization: Bearer <token>` (JWT), que navegadores nunca enviam automaticamente, tornando-as imunes a CSRF por natureza.

---

## Estratégia Implementada: Synchronizer Token Pattern

Inspirada na abordagem do CodeIgniter, a proteção funciona em três etapas:

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. GERAÇÃO (GET — carregamento do formulário)                   │
│                                                                 │
│   ControllerMain → Csrf::getToken()                             │
│     ├─ se token ausente/expirado → gera bin2hex(random_bytes)   │
│     └─ armazena em $_SESSION['csrf_token'] + timestamp          │
│                                                                 │
│   Template renderiza: <input type="hidden" name="csrf_token"    │
│                              value="a3f8...">                   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼ usuário envia formulário
┌─────────────────────────────────────────────────────────────────┐
│ 2. VALIDAÇÃO (POST — processamento)                             │
│                                                                 │
│   ControllerMain.__construct()                                  │
│     ├─ lê $_POST['csrf_token']                                  │
│     │    ou header X-CSRF-Token (para AJAX)                     │
│     ├─ compara com $_SESSION['csrf_token'] via hash_equals()    │
│     └─ verifica TTL (CSRF_EXPIRE segundos)                      │
│                                                                 │
│   Token inválido → HTTP 419 + redirect com mensagem de erro     │
│   Token válido   → execução normal continua                     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. LIMPEZA (automática)                                         │
│                                                                 │
│   Request::getPost() remove csrf_token do array retornado      │
│   → o token nunca chega ao Model nem ao banco de dados          │
└─────────────────────────────────────────────────────────────────┘
```

---

## Arquivos Envolvidos

| Arquivo | Papel |
|---|---|
| `core/Library/Csrf.php` | Classe principal: geração, leitura e validação do token |
| `core/Library/ControllerMain.php` | Valida o token automaticamente em toda requisição mutante |
| `core/Library/Request.php` | Remove `csrf_token` de `getPost()` antes de chegar ao Model |
| `app/Config/Constants.php` | Constantes de configuração com valores padrão |
| `app/Helper/formHelper.php` | Helper `csrfField()` para inserir o campo nos formulários |
| `app/View/Layout/default.php` | `<meta name="csrf-token">` no `<head>` para uso em AJAX |

---

## Configuração

As constantes são definidas em `app/Config/Constants.php` e podem ser ajustadas diretamente no arquivo:

```php
// Ativa/desativa a proteção CSRF (padrão: true)
define('CSRF_PROTECTION', true);

// Nome do campo hidden e da chave de sessão
define('CSRF_TOKEN_NAME', 'csrf_token');

// Header HTTP aceito em requisições AJAX
define('CSRF_HEADER_NAME', 'X-CSRF-Token');

// Vida do token em segundos (padrão: 7200 = 2 horas)
define('CSRF_EXPIRE', 7200);

// true = gera novo token a cada POST válido
// false = token estável durante o TTL (padrão)
define('CSRF_REGENERATE', false);

// URIs excluídas da validação (array de substrings)
define('CSRF_EXCLUDE_URIS', []);
```

> **CSRF_REGENERATE:** Quando `true`, cada envio de formulário invalida o token anterior. Isso aumenta a segurança, mas impede o uso de múltiplas abas do mesmo formulário simultaneamente. Mantenha `false` para a maioria dos casos de uso acadêmico.

---

## Uso em Formulários

### Formulários HTML (padrão)

Insira `<?= csrfField() ?>` como **primeiro filho** de todo `<form method="POST">`:

```php
<form method="POST" action="/categoria/insert">

    <?= csrfField() ?>  <!-- gera: <input type="hidden" name="csrf_token" value="..."> -->

    <input type="text" name="descricao">
    <button type="submit">Salvar</button>

</form>
```

O helper `csrfField()` está disponível em todas as views porque é carregado automaticamente por `ControllerMain` via `formHelper.php`.

### Formulários com JavaScript (AJAX / fetch)

Para requisições AJAX, leia o token da meta tag injetada no layout e envie como header:

```javascript
// Lê o token do <meta name="csrf-token"> no <head>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

fetch('/produto/insert', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken        // ← header alternativo ao campo hidden
    },
    body: JSON.stringify({ descricao: 'Produto X' })
});
```

O servidor aceita o token tanto via campo `$_POST['csrf_token']` quanto via header `X-CSRF-Token` — ambos são verificados pelo `ControllerMain`.

### Formulários enviados via JavaScript sem fetch (ex: `form.submit()`)

O token já está no campo hidden do formulário, então `form.submit()` o envia automaticamente:

```javascript
// O campo hidden csrf_token já está no form — nenhuma ação extra necessária
const form = document.getElementById('meuForm');
form.action = '/Produto/delete/' + id;
form.submit();  // envia com csrf_token incluso
```

---

## Exclusão de URIs

Se alguma rota precisar ficar fora da proteção CSRF (ex: webhook de pagamento que recebe POST externo), adicione a substring da URI ao array em `Constants.php`:

```php
define('CSRF_EXCLUDE_URIS', [
    '/webhook/pagamento',
    '/integracao/erp',
]);
```

> Use exclusões com cautela — qualquer URI excluída fica vulnerável a CSRF.

---

## Como o Token é Protegido

| Mecanismo | Detalhe |
|---|---|
| **Geração segura** | `bin2hex(random_bytes(32))` — 64 caracteres hexadecimais, 256 bits de entropia |
| **Armazenamento** | `$_SESSION` no servidor — o token nunca fica exposto apenas no lado do cliente |
| **Comparação** | `hash_equals()` — tempo constante, previne timing attacks |
| **Expiração** | TTL de 2 horas por padrão (`CSRF_EXPIRE`), após isso o token é regenerado |
| **Limpeza** | `Request::getPost()` remove o campo antes de chegar ao Model/banco |
| **API isolada** | Rotas `/api/*` usam roteador separado (`ApiRoutes`), sem passar pelo `ControllerMain` |

---

## Referência da Classe `Csrf`

```php
namespace Core\Library;

// Gera novo token e armazena na sessão
Csrf::generate(): string

// Retorna token atual; gera novo se ausente ou expirado
Csrf::getToken(): string

// Retorna o nome do campo configurado (CSRF_TOKEN_NAME)
Csrf::getTokenName(): string

// Retorna o <input type="hidden"> pronto para inserção no HTML
Csrf::getHiddenField(): string

// Valida token recebido; retorna false se inválido/ausente/expirado
Csrf::validate(?string $token): bool

// Verifica se a URI atual está em CSRF_EXCLUDE_URIS
Csrf::isExcluded(): bool
```

---

## Resposta em Caso de Falha

Quando o token é inválido, ausente ou expirado:

- **Status HTTP:** `419 — Authentication Timeout` (token inválido)
- **Comportamento:** redirect para `/Home/viewErros` com mensagem flash de erro
- **Mensagem exibida:** *"Token de segurança inválido. Recarregue a página e tente novamente."*

O usuário pode simplesmente recarregar o formulário (GET) para obter um novo token válido.
