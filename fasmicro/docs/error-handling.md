# Tratamento de Erros — ErrorHandler

O FasMicro possui um sistema centralizado de tratamento de erros registrado automaticamente no `public/index.php`.

---

## Como funciona

`ErrorHandler::register()` registra três handlers globais PHP:

| Handler PHP | Classe FasMicro | Captura |
|---|---|---|
| `set_exception_handler` | `handleException()` | Exceções não capturadas em qualquer camada |
| `set_error_handler` | `handleError()` | Erros PHP (`E_WARNING`, `E_NOTICE`, etc.) — convertidos em `ErrorException` |
| `register_shutdown_function` | `handleShutdown()` | Erros fatais (`E_ERROR`, `E_PARSE`, `E_CORE_ERROR`) |

---

## Comportamento por contexto

### Contexto Web

Exibe uma página HTML com Bootstrap mostrando o código HTTP e uma mensagem amigável.

Com `APP_DEBUG=true` no `.env`, a página exibe também o arquivo, a linha e o stack trace completo da exceção.

### Contexto de API (`/api/...`)

Retorna uma resposta JSON no padrão do `ApiResponse`:

```json
{
  "status": "error",
  "code": 500,
  "message": "Erro interno do servidor."
}
```

Com `APP_DEBUG=true`, a mensagem real da exceção é incluída.

---

## Modo debug

Adicione ao `.env`:

```ini
APP_DEBUG=true
```

> **Atenção:** nunca habilite `APP_DEBUG=true` em produção — exposição de stack traces é um risco de segurança.

---

## Lançando exceções com código HTTP

Para retornar um código HTTP específico, lance a exceção com o código como segundo argumento:

```php
throw new \RuntimeException("Produto não encontrado.", 404);
throw new \RuntimeException("Acesso não autorizado.", 403);
throw new \Exception("Dados inválidos.", 400);
```

Códigos fora da faixa 400–599 são mapeados para 500.

---

## Erros de rota (404)

`Erros::controllerNotFound()` e `Erros::methodNotFound()` — chamados pelo `Routes.php` — agora delegam para `ErrorHandler`, produzindo:

- **Web:** página 404 padronizada com link para a home
- **API:** `{"status":"error","code":404,"message":"..."}`

---

## Logging automático de exceções

O `ErrorHandler` integra-se com o `Logger` e registra **toda** exceção não capturada no arquivo de log, independentemente do modo debug:

| Código HTTP | Nível de log | Exemplo |
|---|---|---|
| 5xx | `critical` | Exceção sem código, falha de DB, erro de parse |
| 4xx | `warning` | 404 Not Found, 403 Forbidden, 419 CSRF |

Dados registrados em cada entrada: código HTTP, mensagem, arquivo, linha, URI, método HTTP, IP do cliente e stack trace completo.

O nível mínimo de log é configurável via `LOG_LEVEL` no `.env`:

```ini
LOG_LEVEL=debug    ; desenvolvimento — registra tudo
LOG_LEVEL=error    ; produção — registra apenas falhas críticas e erros
```

Consulte [docs/logging.md](logging.md) para a documentação completa do `Logger`.

---

## Exemplo — captura manual em controller

Para cenários onde você quer um comportamento específico além do handler global:

```php
try {
    $this->db->beginTransaction();
    // ... operações
    $this->db->commit();
} catch (\Exception $e) {
    $this->db->rollback();
    // Relança para o handler global tratar
    throw $e;
}
```

Ou trate localmente e não relance — o handler global só atua para exceções **não capturadas**.
