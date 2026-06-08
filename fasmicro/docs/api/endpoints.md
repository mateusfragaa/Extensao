# Endpoints

Todos os endpoints abaixo têm como prefixo `/api/v1/`.

---

## Autenticação

| Método | Endpoint          | Auth | Descrição                          |
|--------|-------------------|------|------------------------------------|
| POST   | `/auth/login`     | Não  | Gera access_token e refresh_token  |
| POST   | `/auth/refresh`   | Não  | Renova o access_token              |
| GET    | `/auth/me`        | Sim  | Retorna dados do usuário logado    |

> Consulte [authentication.md](authentication.md) para exemplos completos de request/response.

---

## Produtos

Todos os endpoints de produtos exigem autenticação (Bearer Token).
Operações de escrita (POST, PUT, PATCH, DELETE) exigem nível ≤ 11 (Administrador).

### GET /api/v1/produtos

Lista todos os produtos com paginação.

**Query string disponível:**

| Parâmetro  | Padrão               | Descrição                        |
|------------|----------------------|----------------------------------|
| `page`     | `1`                  | Página atual                     |
| `per_page` | `15`                 | Registros por página (máx. 100)  |
| `order_by` | `produto.descricao`  | Campo para ordenação             |
| `busca`    | —                    | Filtra por descrição do produto  |

**Request:**

```http
GET /api/v1/produtos?page=1&per_page=10&busca=arroz
Authorization: Bearer <access_token>
```

**Response — 200 OK:**

```json
{
  "status": "success",
  "code": 200,
  "data": [
    {
      "id": 1,
      "descricao": "Arroz tipo 1",
      "complemento": "Pacote 5kg",
      "precoVenda": 25.90,
      "saldoEstoque": 100.0,
      "statusRegistro": 1,
      "categoria_id": 3,
      "nomeCategoria": "Grãos",
      "unidademedida_id": 2,
      "siglaUnidade": "KG"
    }
  ],
  "meta": {
    "total": 1,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

### GET /api/v1/produtos/{id}

Retorna um produto pelo ID.

**Request:**

```http
GET /api/v1/produtos/1
Authorization: Bearer <access_token>
```

**Response — 200 OK:**

```json
{
  "status": "success",
  "code": 200,
  "data": {
    "id": 1,
    "descricao": "Arroz tipo 1",
    "complemento": "Pacote 5kg",
    "precoVenda": 25.90,
    "saldoEstoque": 100.0,
    "statusRegistro": 1,
    "categoria_id": 3,
    "nomeCategoria": "Grãos",
    "unidademedida_id": 2,
    "siglaUnidade": "KG"
  }
}
```

**Erros possíveis:**

| Código | Causa                   |
|--------|-------------------------|
| 401    | Token ausente ou inválido |
| 404    | Produto não encontrado  |

---

### POST /api/v1/produtos

Cria um novo produto. Requer nível ≤ 11.

**Request:**

```http
POST /api/v1/produtos
Authorization: Bearer <access_token>
Content-Type: application/json
```

```json
{
  "descricao": "Feijão carioca",
  "complemento": "Pacote 1kg",
  "categoria_id": 3,
  "unidademedida_id": 2,
  "precoVenda": 8.50,
  "saldoEstoque": 200,
  "statusRegistro": 1
}
```

**Campos permitidos:**

| Campo              | Tipo    | Obrigatório | Descrição                        |
|--------------------|---------|-------------|----------------------------------|
| `descricao`        | string  | Sim         | Nome do produto                  |
| `complemento`      | string  | Não         | Descrição adicional              |
| `categoria_id`     | int     | Sim         | ID da categoria                  |
| `unidademedida_id` | int     | Sim         | ID da unidade de medida          |
| `precoVenda`       | decimal | Sim         | Preço de venda                   |
| `saldoEstoque`     | decimal | Não         | Saldo em estoque                 |
| `statusRegistro`   | int     | Não         | `1` = ativo, `0` = inativo       |

**Response — 201 Created:**

```json
{
  "status": "success",
  "code": 201,
  "data": {
    "id": 42,
    "descricao": "Feijão carioca",
    ...
  }
}
```

**Erros possíveis:**

| Código | Causa                          |
|--------|--------------------------------|
| 401    | Token ausente ou inválido      |
| 403    | Nível de acesso insuficiente   |
| 422    | Falha na validação dos campos  |
| 500    | Falha ao inserir no banco      |

---

### PUT /api/v1/produtos/{id}

Substitui completamente um produto. Todos os campos obrigatórios devem ser enviados. Requer nível ≤ 11.

**Request:**

```http
PUT /api/v1/produtos/42
Authorization: Bearer <access_token>
Content-Type: application/json
```

```json
{
  "descricao": "Feijão carioca premium",
  "complemento": "Pacote 2kg",
  "categoria_id": 3,
  "unidademedida_id": 2,
  "precoVenda": 15.90,
  "saldoEstoque": 150,
  "statusRegistro": 1
}
```

**Response — 200 OK:** retorna o produto atualizado (mesmo formato do `show`).

**Erros possíveis:**

| Código | Causa                          |
|--------|--------------------------------|
| 401    | Token ausente ou inválido      |
| 403    | Nível de acesso insuficiente   |
| 404    | Produto não encontrado         |
| 422    | Falha na validação dos campos  |

---

### PATCH /api/v1/produtos/{id}

Atualiza parcialmente um produto — envie apenas os campos a alterar. Requer nível ≤ 11.

**Request:**

```http
PATCH /api/v1/produtos/42
Authorization: Bearer <access_token>
Content-Type: application/json
```

```json
{
  "precoVenda": 12.90
}
```

**Response — 200 OK:** retorna o produto atualizado.

**Erros possíveis:**

| Código | Causa                          |
|--------|--------------------------------|
| 400    | Body vazio                     |
| 401    | Token ausente ou inválido      |
| 403    | Nível de acesso insuficiente   |
| 404    | Produto não encontrado         |
| 422    | Falha na validação dos campos enviados |

---

### DELETE /api/v1/produtos/{id}

Exclui um produto. Requer nível ≤ 11.

**Request:**

```http
DELETE /api/v1/produtos/42
Authorization: Bearer <access_token>
```

**Response — 204 No Content** (sem body).

**Erros possíveis:**

| Código | Causa                          |
|--------|--------------------------------|
| 401    | Token ausente ou inválido      |
| 403    | Nível de acesso insuficiente   |
| 404    | Produto não encontrado         |
| 500    | Falha ao excluir no banco      |

---

## Resumo geral dos endpoints

| Método | Endpoint                  | Auth | Nível mín. | Descrição                |
|--------|---------------------------|------|------------|--------------------------|
| POST   | `/auth/login`             | Não  | —          | Login                    |
| POST   | `/auth/refresh`           | Não  | —          | Renovar token            |
| GET    | `/auth/me`                | Sim  | qualquer   | Dados do usuário logado  |
| GET    | `/produtos`               | Sim  | qualquer   | Listar produtos          |
| GET    | `/produtos/{id}`          | Sim  | qualquer   | Detalhe de produto       |
| POST   | `/produtos`               | Sim  | 11         | Criar produto            |
| PUT    | `/produtos/{id}`          | Sim  | 11         | Substituir produto       |
| PATCH  | `/produtos/{id}`          | Sim  | 11         | Atualizar produto        |
| DELETE | `/produtos/{id}`          | Sim  | 11         | Excluir produto          |
