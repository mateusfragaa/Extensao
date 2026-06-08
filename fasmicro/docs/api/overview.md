# FasMicro API — Visão Geral

A API REST do FasMicro segue o padrão RESTful com autenticação via JWT (Bearer Token).

## URL Base

```
http://seusite/api/v1/
```

> O prefixo `/api/v1/` é fixo. A versão (`v1`) está embutida na URL para facilitar versionamento futuro.

## Formato de dados

Todas as requisições e respostas utilizam **JSON**.

Para requisições com body (POST, PUT, PATCH), envie o header:

```
Content-Type: application/json
```

## Versionamento

O versionamento é feito via URL. Versão atual: **v1**.

Quando uma nova versão for lançada, a v1 continuará funcionando até ser descontinuada com aviso prévio.

## Autenticação

A API usa **JWT (JSON Web Token)** com algoritmo **HS256**.

Endpoints protegidos exigem o header:

```
Authorization: Bearer <access_token>
```

Consulte [authentication.md](authentication.md) para detalhes completos.

## Níveis de acesso

O sistema usa um modelo onde **menor número = maior privilégio**:

| Nível | Perfil         |
|-------|----------------|
| 1     | Super Admin    |
| 11    | Administrador  |
| 21    | Usuário comum  |

Endpoints que alteram dados (POST, PUT, PATCH, DELETE) exigem nível ≤ 11.

## CORS

A API suporta CORS. A origem permitida é configurada via variável de ambiente:

```ini
API_CORS_ORIGIN=*           ; qualquer origem (desenvolvimento)
API_CORS_ORIGIN=https://meusite.com  ; restrito (produção)
```

Métodos permitidos: `GET, POST, PUT, PATCH, DELETE, OPTIONS`

Headers permitidos: `Content-Type, Authorization, X-Requested-With, X-HTTP-Method-Override`

## Variáveis de ambiente (API)

| Variável            | Descrição                                      | Padrão  |
|---------------------|------------------------------------------------|---------|
| `JWT_SECRET`        | Chave secreta para assinar os tokens (≥32 chars) | —      |
| `JWT_EXPIRE`        | Expiração do access token (segundos)           | `3600`  |
| `JWT_REFRESH_EXPIRE`| Expiração do refresh token (segundos)          | `604800`|
| `API_CORS_ORIGIN`   | Origem CORS permitida                          | `*`     |

> **Importante:** Em produção, defina um `JWT_SECRET` longo e aleatório e restrinja `API_CORS_ORIGIN` ao domínio do frontend.

## Recursos disponíveis

| Recurso     | Prefixo              | Autenticação |
|-------------|----------------------|--------------|
| Autenticação| `/api/v1/auth/`      | Parcial      |
| Produtos    | `/api/v1/produtos/`  | Obrigatória  |

## Códigos HTTP utilizados

| Código | Significado                  |
|--------|------------------------------|
| 200    | OK                           |
| 201    | Criado com sucesso           |
| 204    | Sem conteúdo (ex.: DELETE)   |
| 400    | Requisição inválida          |
| 401    | Não autenticado              |
| 403    | Sem permissão                |
| 404    | Recurso não encontrado       |
| 405    | Método não permitido         |
| 422    | Erro de validação            |
| 500    | Erro interno do servidor     |
