# Formato das Respostas

Todas as respostas da API são em **JSON** com o header `Content-Type: application/json; charset=UTF-8`.

---

## Resposta de sucesso

```json
{
  "status": "success",
  "code": 200,
  "data": { }
}
```

| Campo    | Tipo            | Descrição                          |
|----------|-----------------|------------------------------------|
| `status` | string          | Sempre `"success"`                 |
| `code`   | int             | Código HTTP da resposta            |
| `data`   | object ou array | Dados retornados pelo endpoint     |

### Sucesso com paginação

Quando a resposta é uma lista paginada, o campo `meta` é incluído:

```json
{
  "status": "success",
  "code": 200,
  "data": [ ],
  "meta": {
    "total": 120,
    "per_page": 15,
    "current_page": 1,
    "last_page": 8
  }
}
```

| Campo          | Tipo | Descrição                            |
|----------------|------|--------------------------------------|
| `total`        | int  | Total de registros encontrados       |
| `per_page`     | int  | Registros por página                 |
| `current_page` | int  | Página atual                         |
| `last_page`    | int  | Última página disponível             |

### Parâmetros de paginação (query string)

| Parâmetro  | Padrão | Máximo | Descrição          |
|------------|--------|--------|--------------------|
| `page`     | `1`    | —      | Página desejada    |
| `per_page` | `15`   | `100`  | Registros por página|

Exemplo: `GET /api/v1/produtos?page=2&per_page=20`

---

## Resposta de criação — 201 Created

```json
{
  "status": "success",
  "code": 201,
  "data": { }
}
```

---

## Resposta sem conteúdo — 204 No Content

Retornado em operações de exclusão bem-sucedidas. **Sem body.**

---

## Resposta de erro

```json
{
  "status": "error",
  "code": 400,
  "message": "Descrição do erro"
}
```

| Campo     | Tipo   | Descrição                        |
|-----------|--------|----------------------------------|
| `status`  | string | Sempre `"error"`                 |
| `code`    | int    | Código HTTP do erro              |
| `message` | string | Descrição legível do erro        |

### Erro de validação — 422 Unprocessable Entity

Quando há falha de validação campo a campo, o campo `errors` é incluído:

```json
{
  "status": "error",
  "code": 422,
  "message": "Dados inválidos",
  "errors": {
    "descricao": "O campo descricao é obrigatório",
    "precoVenda": "O campo precoVenda deve ser numérico"
  }
}
```

| Campo    | Tipo   | Descrição                                        |
|----------|--------|--------------------------------------------------|
| `errors` | object | Mapa de `{ campo: "mensagem de erro" }`          |

---

## Tabela de códigos HTTP

| Código | Método               | Quando ocorre                              |
|--------|----------------------|--------------------------------------------|
| 200    | `success()`          | Requisição bem-sucedida                    |
| 201    | `created()`          | Recurso criado (POST)                      |
| 204    | `noContent()`        | Recurso excluído (DELETE)                  |
| 400    | `badRequest()`       | Dados ausentes ou inválidos                |
| 401    | `unauthorized()`     | Token ausente, inválido ou expirado        |
| 403    | `forbidden()`        | Token válido mas nível de acesso insuficiente |
| 404    | `notFound()`         | Recurso ou rota não encontrada             |
| 405    | `methodNotAllowed()` | Método HTTP não suportado na rota          |
| 422    | `validationError()`  | Falha nas regras de validação do model     |
| 500    | `serverError()`      | Erro interno (model não encontrado, etc.)  |
