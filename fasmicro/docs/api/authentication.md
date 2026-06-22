# Autenticação

A API usa **JWT (JSON Web Token)** com algoritmo **HS256**. O fluxo envolve dois tokens:

| Token           | Duração padrão | Finalidade                              |
|-----------------|----------------|-----------------------------------------|
| `access_token`  | 1 hora         | Autenticar requisições protegidas       |
| `refresh_token` | 7 dias         | Obter novo access token sem novo login  |

---

## POST /api/v1/auth/login

Autentica o usuário e retorna os tokens.

**Não requer autenticação.**

### Request

```http
POST /api/v1/auth/login
Content-Type: application/json
```

```json
{
  "email": "usuario@exemplo.com",
  "senha": "minhasenha"
}
```

### Response — 200 OK

```json
{
  "status": "success",
  "code": 200,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "nome": "João Silva",
      "email": "usuario@exemplo.com",
      "nivel": 11
    }
  }
}
```

### Erros possíveis

| Código | Mensagem                          | Causa                              |
|--------|-----------------------------------|------------------------------------|
| 400    | `Os campos email e senha são obrigatórios` | Campo ausente          |
| 400    | `Formato de e-mail inválido`      | E-mail fora do formato RFC         |
| 401    | `Credenciais inválidas`           | Usuário não encontrado, inativo ou senha errada |

> A mensagem de erro é genérica intencionalmente para não revelar se o e-mail existe no sistema.

---

## POST /api/v1/auth/refresh

Gera um novo `access_token` usando o `refresh_token`.

**Não requer autenticação.**

### Request

```http
POST /api/v1/auth/refresh
Content-Type: application/json
```

```json
{
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

### Response — 200 OK

```json
{
  "status": "success",
  "code": 200,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

### Erros possíveis

| Código | Mensagem                                      | Causa                          |
|--------|-----------------------------------------------|--------------------------------|
| 400    | `O campo refresh_token é obrigatório`         | Campo ausente                  |
| 401    | `Refresh token inválido ou expirado`          | Token corrompido ou expirado   |
| 401    | `Token informado não é um refresh token`      | Enviou um access_token         |
| 401    | `Usuário não encontrado ou inativo`           | Conta desativada após o login  |

---

## GET /api/v1/auth/me

Retorna os dados do usuário autenticado.

**Requer autenticação.**

### Request

```http
GET /api/v1/auth/me
Authorization: Bearer <access_token>
```

### Response — 200 OK

```json
{
  "status": "success",
  "code": 200,
  "data": {
    "id": 1,
    "nome": "João Silva",
    "email": "usuario@exemplo.com",
    "nivel": 11
  }
}
```

### Erros possíveis

| Código | Mensagem                   | Causa                          |
|--------|----------------------------|--------------------------------|
| 401    | `Token não fornecido`      | Header Authorization ausente   |
| 401    | `Assinatura inválida`      | Token adulterado               |
| 401    | `Token expirado`           | Access token vencido           |
| 404    | `Usuário não encontrado`   | Usuário deletado após o login  |

---

## Como usar o token nas requisições

Inclua o `access_token` no header de todas as requisições protegidas:

```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

---

## Fluxo recomendado

```
1. POST /auth/login           → obtém access_token + refresh_token
2. Requisições normais        → usa Authorization: Bearer <access_token>
3. access_token expirado      → POST /auth/refresh com o refresh_token
4. refresh_token expirado     → volta ao passo 1 (novo login)
```

---

## Estrutura do JWT payload (access token)

| Campo  | Tipo   | Descrição                              |
|--------|--------|----------------------------------------|
| `sub`  | int    | ID do usuário                          |
| `nome` | string | Nome do usuário                        |
| `email`| string | E-mail do usuário                      |
| `nivel`| int    | Nível de acesso (menor = mais permissão)|
| `iat`  | int    | Timestamp de emissão (Unix)            |
| `exp`  | int    | Timestamp de expiração (Unix)          |
