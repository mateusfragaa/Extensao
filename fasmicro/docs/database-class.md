# Classe Database — Referência Completa

A classe `Database` (`core/Library/Database.php`) é a camada de abstração de banco de dados do FasMicro. Ela oferece dois modos de uso:

- **Query Builder** — interface fluente (encadeamento de métodos) para construir queries sem escrever SQL.
- **Métodos raw** — execução direta de SQL para casos mais complexos.

Suporta **MySQL** e **SQL Server** via PDO.

---

## Instanciação

A classe nunca é instanciada diretamente nos Controllers. O `ModelMain` cria e configura a instância automaticamente a partir das variáveis do `.env`:

```php
// ModelMain.php (acontece automaticamente)
$this->db = new Database(
    $_ENV['DB_CONNECTION'],  // mysql | sqlsrv
    $_ENV['DB_HOST'],
    $_ENV['DB_PORT'],
    $_ENV['DB_DATABASE'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD']
);
$this->db->table($this->table); // define a tabela do Model
```

Nos seus Models, acesse o banco via `$this->db`:

```php
class ProdutoModel extends ModelMain
{
    protected $table = "produto";

    public function lista()
    {
        return $this->db->orderBy("descricao")->findAll();
    }
}
```

---

## Query Builder

Todos os métodos do Query Builder retornam `$this`, permitindo encadeamento. A query só é executada ao chamar `findAll()`, `first()`, `findCount()`, `insert()`, `update()` ou `delete()`.

---

### `select(string $columns = "*")`

Define as colunas a serem retornadas.

```php
// Seleciona todas as colunas (padrão)
$this->db->findAll();

// Colunas específicas
$this->db
    ->select("id, descricao, precoVenda")
    ->findAll();

// Com alias e colunas de outras tabelas (usado com join)
$this->db
    ->select("produto.*, categoria.descricao AS nomeCategoria")
    ->join("categoria", "produto.categoria_id = categoria.id")
    ->findAll();
```

---

### `table(string $table)`

Define a tabela alvo. Já é chamado automaticamente pelo `ModelMain`. Use quando precisar trocar de tabela em tempo de execução.

```php
$this->db->table("produto")->findAll();
```

---

### `join(string $table, string $condition, string $tipoJoin = "INNER")`

Adiciona um JOIN à query. Pode ser encadeado múltiplas vezes.

**Parâmetros:**
| Parâmetro | Tipo | Padrão | Descrição |
|---|---|---|---|
| `$table` | string | — | Nome da tabela a fazer JOIN |
| `$condition` | string | — | Condição ON |
| `$tipoJoin` | string | `"INNER"` | Tipo: `INNER`, `LEFT`, `RIGHT` |

```php
// INNER JOIN (padrão)
$this->db
    ->select("produto.*, categoria.descricao AS nomeCategoria, unidademedida.sigla")
    ->join("categoria", "produto.categoria_id = categoria.id")
    ->join("unidademedida", "produto.unidademedida_id = unidademedida.id")
    ->orderBy("produto.descricao")
    ->findAll();

// LEFT JOIN
$this->db
    ->select("usuario.nome, pedido.id AS pedido_id")
    ->join("pedido", "usuario.id = pedido.usuario_id", "LEFT")
    ->findAll();
```

---

### `where(string|array $condition, mixed $params = "")`

Adiciona uma condição WHERE com operador `AND`.

```php
// Condição simples (campo = valor)
$this->db->where("id", 5)->first();

// Com operador explícito
$this->db->where("precoVenda >=", 10.00)->findAll();
$this->db->where("statusRegistro <>", 2)->findAll();

// Múltiplas condições com array (todas unidas por AND)
$this->db->where([
    "statusRegistro" => 1,
    "categoria_id"   => 3
])->findAll();

// Encadeando múltiplos where (AND implícito)
$this->db
    ->where("statusRegistro", 1)
    ->where("precoVenda >=", 50.00)
    ->orderBy("descricao")
    ->findAll();
```

**Operadores suportados:** `=`, `>=`, `<=`, `>`, `<`, `<>`

---

### `orWhere(string|array $condition, mixed $params = "")`

Igual ao `where()`, mas concatena com `OR`.

```php
$this->db
    ->where("statusRegistro", 1)
    ->orWhere("statusRegistro", 3)
    ->findAll();
```

---

### `whereIn(string $field, array $params, string $operadorLogico = "AND")`

Filtra registros onde o campo está dentro de uma lista de valores.

```php
// Produtos das categorias 1, 2 e 5
$this->db->whereIn("categoria_id", [1, 2, 5])->findAll();

// Combinado com outro where
$this->db
    ->where("statusRegistro", 1)
    ->whereIn("id", [10, 20, 30])
    ->findAll();

// Com OR
$this->db
    ->where("statusRegistro", 1)
    ->whereIn("categoria_id", [1, 2], "OR")
    ->findAll();
```

---

### `whereNotIn(string $field, array $params, string $operadorLogico = "AND")`

Filtra registros onde o campo **não está** na lista.

```php
// Todos os produtos exceto os das categorias 3 e 4
$this->db->whereNotIn("categoria_id", [3, 4])->findAll();
```

---

### `whereLike(string $field, string $value, string $operadorLogico = "AND")`

Busca por similaridade. O valor é automaticamente envolto em `%valor%`.

```php
// Busca produtos que contêm "arroz" na descrição
$this->db->whereLike("descricao", "arroz")->findAll();

// Combinado
$this->db
    ->where("statusRegistro", 1)
    ->whereLike("descricao", $termoBusca)
    ->orderBy("descricao")
    ->findAll();
```

---

### `whereBetween(string $field, mixed $valorIni, mixed $valorFim, string $operadorLogico = "AND")`

Filtra registros onde o campo está entre dois valores (inclusivo).

```php
// Produtos com preço entre R$ 10 e R$ 100
$this->db->whereBetween("precoVenda", 10.00, 100.00)->findAll();

// Por intervalo de datas
$this->db->whereBetween("created_at", "2026-01-01", "2026-05-31")->findAll();

// Com AND (padrão) combinado a outro filtro
$this->db
    ->where("statusRegistro", 1)
    ->whereBetween("saldoEstoque", 0, 100)
    ->findAll();
```

---

### `group(string $operadorLogico = "AND")` / `endGroup()`

Agrupa condições WHERE com parênteses para controle de precedência lógica.

```php
// WHERE statusRegistro = 1 AND (categoria_id = 1 OR categoria_id = 2)
$this->db
    ->where("statusRegistro", 1)
    ->group()
        ->where("categoria_id", 1)
        ->orWhere("categoria_id", 2)
    ->endGroup()
    ->findAll();
```

---

### `groupBy(string $column)`

Agrupa os resultados por uma ou mais colunas.

```php
$this->db
    ->select("categoria_id, COUNT(*) AS total")
    ->groupBy("categoria_id")
    ->findAll();

// Múltiplas colunas
$this->db
    ->select("categoria_id, statusRegistro, COUNT(*) AS total")
    ->groupBy("categoria_id, statusRegistro")
    ->findAll();
```

---

### `having(string|array $condition, mixed $params = "")` / `orHaving(...)`

Filtra grupos após o `GROUP BY`. Funciona igual ao `where()`, mas gera cláusula `HAVING`.

```php
// Categorias com mais de 5 produtos
$this->db
    ->select("categoria_id, COUNT(*) AS total")
    ->groupBy("categoria_id")
    ->having("COUNT(*) >", 5)
    ->findAll();

// Com OR
$this->db
    ->select("categoria_id, COUNT(*) AS total")
    ->groupBy("categoria_id")
    ->having("COUNT(*) >", 10)
    ->orHaving("COUNT(*) <", 2)
    ->findAll();
```

---

### `orderBy(string $column, string $direction = "ASC")`

Define a ordenação dos resultados.

```php
$this->db->orderBy("descricao")->findAll();
$this->db->orderBy("precoVenda", "DESC")->findAll();
$this->db->orderBy("categoria_id, descricao")->findAll();
```

---

### `limit(int $limit, int|null $offset = null)`

Limita a quantidade de registros retornados, com suporte a paginação.

```php
// Apenas os 10 primeiros
$this->db->limit(10)->findAll();

// Paginação: página 2, 10 itens por página → offset = (2-1) * 10 = 10
$pagina = 2;
$itensPorPagina = 10;
$this->db
    ->orderBy("descricao")
    ->limit($itensPorPagina, ($pagina - 1) * $itensPorPagina)
    ->findAll();
```

> **Nota:** Em SQL Server, `limit()` com offset usa `OFFSET ... FETCH NEXT`; sem offset usa `SELECT TOP`.

---

### `union(string $sql, array $params = [])` / `unionAll(string $sql, array $params = [])`

Combina o resultado com outra query SQL.

```php
// UNION (remove duplicatas)
$this->db
    ->select("id, descricao")
    ->where("statusRegistro", 1)
    ->union("SELECT id, descricao FROM produto_historico WHERE statusRegistro = ?", [1])
    ->orderBy("descricao")
    ->findAll();

// UNION ALL (mantém duplicatas)
$this->db
    ->select("nome AS label, 'usuario' AS tipo")
    ->unionAll("SELECT descricao AS label, 'categoria' AS tipo FROM categoria")
    ->orderBy("label")
    ->findAll();
```

---

## Métodos de Execução

### `findAll()` → `array`

Executa o SELECT e retorna todos os registros como array associativo.

```php
$produtos = $this->db->findAll();
// [['id' => 1, 'descricao' => 'Arroz', ...], ['id' => 2, ...], ...]

foreach ($produtos as $produto) {
    echo $produto['descricao'];
}
```

---

### `first()` → `array`

Retorna apenas o primeiro registro como array associativo. Retorna `[]` se não encontrado.

```php
$produto = $this->db->where("id", 5)->first();

if (!empty($produto)) {
    echo $produto['descricao'];
}
```

---

### `findCount()` → `int`

Retorna o número de linhas que a query retornaria.

```php
$total = $this->db->where("statusRegistro", 1)->findCount();
// Útil para paginação

$totalPaginas = ceil($total / $itensPorPagina);
```

---

### `insert(array $data)` → `int`

Insere um registro e retorna o ID gerado (`lastInsertId`). Retorna `0` em caso de erro.

```php
$id = $this->db->insert([
    'descricao'        => 'Feijão Preto',
    'complemento'      => 'Pacote 1kg',
    'categoria_id'     => 2,
    'unidademedida_id' => 1,
    'precoVenda'       => 5.90,
    'saldoEstoque'     => 100,
    'statusRegistro'   => 1,
]);

if ($id > 0) {
    echo "Produto inserido com ID: $id";
}
```

> A tabela já deve estar definida via `$this->db->table("produto")` ou pelo ModelMain.

---

### `update(array $data)` → `int`

Atualiza os registros que atendem ao `where()`. Retorna o número de linhas afetadas. Retorna `-1` em caso de erro.

```php
$linhasAfetadas = $this->db
    ->where("id", 5)
    ->update([
        'precoVenda'    => 6.50,
        'statusRegistro' => 1,
    ]);

if ($linhasAfetadas > 0) {
    echo "Registro atualizado.";
}
```

**Com JOIN (para atualizar via relacionamento):**

```php
// Atualiza status dos produtos de uma categoria específica
$this->db
    ->join("categoria", "produto.categoria_id = categoria.id")
    ->where("categoria.descricao", "Grãos")
    ->update(['produto.statusRegistro' => 2]);
```

---

### `delete()` → `int`

Exclui os registros que atendem ao `where()`. Retorna o número de linhas excluídas. Retorna `0` em caso de erro.

```php
$excluidos = $this->db
    ->where("id", 5)
    ->delete();

if ($excluidos > 0) {
    echo "Registro excluído.";
}
```

> **Atenção:** Sempre use `where()` antes de `delete()`. Sem condição, todos os registros da tabela serão excluídos.

**Com JOIN:**

```php
// Exclui produtos vinculados a uma categoria
$this->db
    ->join("categoria", "produto.categoria_id = categoria.id")
    ->where("categoria.id", 3)
    ->delete();
```

---

### `dbClear()`

Reseta o estado interno do Query Builder (select, joins, wheres, params etc.). Chamado automaticamente após `findAll()`, `first()`, `findCount()`, `insert()`, `update()` e `delete()`.

```php
// Uso manual raramente necessário
$this->db->dbClear();
```

---

## Transações

O FasMicro suporta transações PDO via três métodos: `beginTransaction()`, `commit()` e `rollback()`.

Durante uma transação ativa, a classe **reutiliza a mesma conexão PDO** para todas as operações, garantindo atomicidade. A conexão só é liberada após `commit()` ou `rollback()`.

### `beginTransaction()`

Abre uma transação. Lança `\Exception` se já houver uma transação ativa.

### `commit()`

Confirma todas as operações realizadas desde `beginTransaction()` e libera a conexão.

### `rollback()`

Desfaz todas as operações realizadas desde `beginTransaction()` e libera a conexão.

---

### Padrão de uso

```php
$this->db->beginTransaction();
try {
    // Operações atômicas
    $pedidoId = $this->db->table('pedido')->insert($dadosPedido);

    $this->db->table('estoque')
        ->where('produto_id', $produtoId)
        ->update(['saldo' => $novoSaldo]);

    $this->db->commit();
    return $pedidoId;

} catch (\Exception $e) {
    $this->db->rollback();
    throw $e;
}
```

### Exemplo em um Model

```php
class PedidoModel extends ModelMain
{
    protected $table = 'pedido';

    public function registrarPedido(array $pedido, array $itens): int
    {
        $this->db->beginTransaction();
        try {
            $pedidoId = $this->db->table('pedido')->insert($pedido);

            foreach ($itens as $item) {
                $item['pedido_id'] = $pedidoId;
                $this->db->table('pedido_item')->insert($item);

                $this->db->table('estoque')
                    ->where('produto_id', $item['produto_id'])
                    ->update(['saldo' => $item['novoSaldo']]);
            }

            $this->db->commit();
            return $pedidoId;

        } catch (\Exception $e) {
            $this->db->rollback();
            Session::set('msgError', 'Falha ao registrar pedido: ' . $e->getMessage());
            return 0;
        }
    }
}
```

---

## Métodos Raw (SQL Direto)

Use quando a query for complexa demais para o Query Builder.

---

### `dbSelect(string $sql, array|null $params = null)` → `PDOStatement`

Executa uma query SELECT e retorna o PDOStatement para iteração manual.

```php
$sql = "SELECT p.*, c.descricao AS nomeCategoria
        FROM produto p
        INNER JOIN categoria c ON p.categoria_id = c.id
        WHERE p.statusRegistro = ? AND c.id = ?";

$rs = $this->db->dbSelect($sql, [1, 3]);

// Busca registro por registro
while ($row = $this->db->dbBuscaArray($rs)) {
    echo $row['descricao'];
}

// Ou todos de uma vez
$rs = $this->db->dbSelect($sql, [1, 3]);
$produtos = $this->db->dbBuscaArrayAll($rs);
```

---

### `dbInsert(string $sql, array|null $params = null)` → `int`

Executa um INSERT e retorna o `lastInsertId`.

```php
$sql = "INSERT INTO produto (descricao, precoVenda, statusRegistro) VALUES (?, ?, ?)";
$id  = $this->db->dbInsert($sql, ['Macarrão', 3.50, 1]);
```

---

### `dbUpdate(string $sql, array|null $params = null)` → `int`

Executa um UPDATE e retorna o número de linhas afetadas.

```php
$sql = "UPDATE produto SET precoVenda = ?, statusRegistro = ? WHERE id = ?";
$linhas = $this->db->dbUpdate($sql, [7.00, 1, 10]);
```

---

### `dbDelete(string $sql, array|null $params = null)` → `int|bool`

Executa um DELETE e retorna o número de linhas excluídas ou `false`.

```php
$sql = "DELETE FROM produto WHERE id = ?";
$resultado = $this->db->dbDelete($sql, [10]);
```

---

## Métodos de Leitura de Resultados (Raw)

Usados em conjunto com `dbSelect()`.

| Método | Retorno | Descrição |
|---|---|---|
| `dbBuscaDados($rs)` | `object` | Retorna o próximo registro como objeto |
| `dbBuscaDadosAll($rs)` | `object[]` | Retorna todos os registros como objetos |
| `dbBuscaArray($rs)` | `array` | Retorna o próximo registro como array associativo |
| `dbBuscaArrayAll($rs)` | `array[]` | Retorna todos os registros como arrays associativos |
| `dbNumeroLinhas($rs)` | `int` | Retorna a quantidade de linhas do resultado |
| `dbNumeroColunas($rs)` | `int` | Retorna a quantidade de colunas do resultado |

```php
$rs = $this->db->dbSelect("SELECT * FROM produto WHERE statusRegistro = ?", [1]);

// Como objeto
while ($obj = $this->db->dbBuscaDados($rs)) {
    echo $obj->descricao;
}

// Como array
$rs = $this->db->dbSelect("SELECT * FROM produto WHERE statusRegistro = ?", [1]);
$todos = $this->db->dbBuscaArrayAll($rs);

// Contagem
$rs = $this->db->dbSelect("SELECT * FROM produto", []);
$qtd = $this->db->dbNumeroLinhas($rs);
```

---

### `dbResultado($rs, string|int $campo)` → `mixed`

Atalho para retornar o valor de um campo específico do primeiro registro.

```php
$rs     = $this->db->dbSelect("SELECT COUNT(*) AS total FROM produto", []);
$total  = $this->db->dbResultado($rs, "total");
echo "Total de produtos: $total";
```

---

## Exemplos Completos

### Listagem com paginação

```php
public function listaPaginada(int $pagina, int $porPagina, string $busca = "")
{
    $offset = ($pagina - 1) * $porPagina;

    $query = $this->db
        ->select("produto.*, categoria.descricao AS nomeCategoria")
        ->join("categoria", "produto.categoria_id = categoria.id")
        ->where("produto.statusRegistro", 1);

    if (!empty($busca)) {
        $query->whereLike("produto.descricao", $busca);
    }

    $total = $this->db
        ->select("COUNT(*) AS total")
        ->join("categoria", "produto.categoria_id = categoria.id")
        ->where("produto.statusRegistro", 1)
        ->findCount();

    $registros = $query
        ->orderBy("produto.descricao")
        ->limit($porPagina, $offset)
        ->findAll();

    return ['registros' => $registros, 'total' => $total];
}
```

### Relatório agrupado

```php
public function totalPorCategoria()
{
    return $this->db
        ->select("categoria.descricao AS categoria, COUNT(produto.id) AS total, SUM(produto.saldoEstoque) AS estoqueTotal")
        ->join("categoria", "produto.categoria_id = categoria.id")
        ->where("produto.statusRegistro", 1)
        ->groupBy("categoria.id, categoria.descricao")
        ->having("COUNT(produto.id) >", 0)
        ->orderBy("categoria.descricao")
        ->findAll();
}
```

### UNION entre tabelas

```php
public function buscaGeral(string $termo)
{
    return $this->db
        ->select("id, descricao, 'produto' AS tipo")
        ->whereLike("descricao", $termo)
        ->union(
            "SELECT id, descricao, 'categoria' AS tipo FROM categoria WHERE descricao LIKE ?",
            ["%{$termo}%"]
        )
        ->orderBy("descricao")
        ->findAll();
}
```
