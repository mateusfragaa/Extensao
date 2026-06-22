# Sistema de Logging — Logger (PSR-3)

O FasMicro possui um sistema de logging estruturado implementado em `core/Library/Logger.php`, com API compatível com a especificação PSR-3.

---

## Funcionamento

Cada chamada ao `Logger` grava uma linha no arquivo de log do dia corrente:

```
storage/logs/app-YYYY-MM-DD.log
```

A rotação é diária e automática — um novo arquivo é criado a cada dia. O diretório `storage/logs/` é criado automaticamente na primeira gravação.

### Formato da entrada

```
[2026-06-06 14:30:15] app.ERROR: Mensagem de erro {"code":500,"file":"/path/arquivo.php","line":42}
```

---

## Níveis de severidade (PSR-3)

Do mais ao menos severo:

| Nível | Constante | Uso típico |
|---|---|---|
| `emergency` | `Logger::EMERGENCY` | Sistema inutilizável |
| `alert` | `Logger::ALERT` | Ação imediata necessária |
| `critical` | `Logger::CRITICAL` | Falha crítica de componente — exceções 5xx |
| `error` | `Logger::ERROR` | Erros de execução sem parada |
| `warning` | `Logger::WARNING` | Situações anômalas — exceções 4xx |
| `notice` | `Logger::NOTICE` | Eventos normais mas significativos |
| `info` | `Logger::INFO` | Eventos informativos (login, criação de registro) |
| `debug` | `Logger::DEBUG` | Informações de depuração |

---

## Configuração

Defina o nível mínimo no `.env` (entradas abaixo do nível configurado são ignoradas):

```ini
; Opções: debug | info | notice | warning | error | critical | alert | emergency
LOG_LEVEL=debug       ; desenvolvimento — registra tudo
;LOG_LEVEL=error      ; produção — registra apenas falhas
```

Se `LOG_LEVEL` não estiver definido, o padrão é `debug`.

---

## Uso

```php
use Core\Library\Logger;

// Informação de negócio
Logger::info('Produto criado', ['id' => $id, 'user' => $userId]);

// Aviso — situação inesperada mas não crítica
Logger::warning('Tentativa de acesso negada', ['ip' => $_SERVER['REMOTE_ADDR'], 'uri' => '/admin']);

// Erro recuperável
Logger::error('Falha ao enviar e-mail', ['to' => $email, 'exception' => $e->getMessage()]);

// Falha crítica
Logger::critical('Conexão com banco de dados perdida', ['exception' => $e->getMessage()]);
```

### Interpolação de contexto (PSR-3)

Use `{chave}` na mensagem para incluir valores do contexto:

```php
Logger::info('Usuário {nome} fez login às {hora}', [
    'nome' => $usuario['nome'],
    'hora' => date('H:i:s'),
]);
// Resultado: "Usuário João fez login às 14:30:15"
```

---

## Integração com ErrorHandler

O `ErrorHandler` chama o `Logger` automaticamente para toda exceção não capturada, antes de responder ao usuário:

| Código HTTP | Nível de log |
|---|---|
| 5xx (500, 503…) | `critical` |
| 4xx (400, 403, 404…) | `warning` |

O contexto registrado inclui: código HTTP, arquivo, linha, URI requisitada, método HTTP, IP do cliente e stack trace completo.

Isso significa que **em produção** (`APP_DEBUG=false`) os detalhes da exceção ficam preservados no log, mesmo que o usuário veja apenas a mensagem genérica.

---

## Referência da API

```php
Logger::emergency(string $message, array $context = []): void
Logger::alert(string $message, array $context = []): void
Logger::critical(string $message, array $context = []): void
Logger::error(string $message, array $context = []): void
Logger::warning(string $message, array $context = []): void
Logger::notice(string $message, array $context = []): void
Logger::info(string $message, array $context = []): void
Logger::debug(string $message, array $context = []): void

// Método genérico — aceita qualquer nível como string
Logger::log(string $level, string $message, array $context = []): void

// Retorna o caminho do arquivo de log do dia atual
Logger::resolveLogFile(): string
```

---

## Localização dos arquivos de log

```
fasmicro/
└── storage/
    └── logs/
        ├── app-2026-06-06.log
        ├── app-2026-06-07.log
        └── ...
```

> Adicione `storage/logs/` ao `.gitignore` para não versionar logs.
