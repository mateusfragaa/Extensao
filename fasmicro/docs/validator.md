# Classe Validator — Referência Completa

A classe `Validator` (`core/Library/Validator.php`) oferece validação de dados para formulários web e entradas de API.

---

## Modos de uso

### `Validator::make()` — uso em Controllers web

Valida os dados e, em caso de erro, armazena os erros e os valores do formulário na sessão, permitindo repopular o formulário. Retorna `true` se houver erros.

```php
$hasErrors = Validator::make($this->request->getPost(), [
    'nome'  => ['label' => 'Nome',  'rules' => 'required|min:3|max:100'],
    'email' => ['label' => 'Email', 'rules' => 'required|email|unique:usuario,email'],
]);

if ($hasErrors) {
    return Redirect::page('Controller/form/novo/0');
}
```

### `Validator::check()` — uso em APIs e lógica pura

Retorna `null` se válido, ou um `array<campo, mensagem>` com os erros. Não toca na sessão.

```php
$errors = Validator::check($dados, $rules);

if ($errors) {
    ApiResponse::validationError($errors);
}
```

---

## Definição de Regras

As regras são passadas como array associativo:

```php
$rules = [
    'campo' => [
        'label' => 'Nome legível do campo (usado nas mensagens)',
        'rules' => 'regra1|regra2|regra3:parametro',
    ],
];
```

---

## Referência de Regras

| Regra | Parâmetro | Descrição |
|---|---|---|
| `required` | — | Campo obrigatório (não pode ser vazio) |
| `nullable` | — | Campo opcional — ignora demais regras se vazio |
| `sometimes` | — | Só valida se o campo estiver presente no array de dados |
| `email` | — | Endereço de e-mail válido |
| `int` | — | Número inteiro |
| `float` | — | Número decimal (aceita vírgula) |
| `min:N` | N = número | Mínimo de N caracteres (string) ou N itens (array) |
| `max:N` | N = número | Máximo de N caracteres (string) ou N itens (array) |
| `confirmed` | — | Deve corresponder ao campo `{campo}_confirmation` |
| `date` | — | Data válida (qualquer formato reconhecido por `strtotime`) |
| `after:X` | data ou `today` | Data deve ser posterior a X |
| `before:X` | data ou `today` | Data deve ser anterior a X |
| `regex:/padrão/` | expressão regular | Deve corresponder à regex informada |
| `in:a,b,c` | lista separada por vírgula | Valor deve estar na lista |
| `not_in:a,b,c` | lista separada por vírgula | Valor não deve estar na lista |
| `url` | — | URL válida |
| `cpf` | — | CPF brasileiro válido (com ou sem formatação) |
| `array` | — | O valor deve ser um array |
| `unique:tabela,coluna` | tabela e coluna | Unicidade no banco de dados |
| `unique:tabela,coluna,exceptId,campoId` | + ID a ignorar | Unicidade ignorando o registro atual (para edições) |
| `mimes:ext1,ext2` | extensões separadas por vírgula | Tipo de arquivo (lê `$_FILES[campo]`) |
| `max_file:KB` | tamanho em KB | Tamanho máximo do arquivo (lê `$_FILES[campo]`) |

---

## Exemplos

### Cadastro de usuário

```php
$rules = [
    'nome'                  => ['label' => 'Nome',           'rules' => 'required|min:3|max:100'],
    'email'                 => ['label' => 'E-mail',         'rules' => 'required|email|unique:usuario,email'],
    'cpf'                   => ['label' => 'CPF',            'rules' => 'required|cpf'],
    'senha'                 => ['label' => 'Senha',          'rules' => 'required|min:8'],
    'senha_confirmation'    => ['label' => 'Confirme senha', 'rules' => 'required'],
    'senha'                 => ['label' => 'Senha',          'rules' => 'required|min:8|confirmed'],
    'site'                  => ['label' => 'Site',           'rules' => 'nullable|url'],
];
```

### Edição de usuário (unique ignorando o próprio registro)

```php
// unique:tabela,coluna,valorAIgnorar,campoId
$rules = [
    'email' => ['label' => 'E-mail', 'rules' => "required|email|unique:usuario,email,{$id},id"],
];
```

### Validação de datas

```php
$rules = [
    'data_nascimento' => ['label' => 'Data de nascimento', 'rules' => 'required|date|before:today'],
    'data_evento'     => ['label' => 'Data do evento',     'rules' => 'required|date|after:today'],
    'data_validade'   => ['label' => 'Validade',           'rules' => 'required|date|after:2026-01-01'],
];
```

### Validação de arquivo

```php
// $_FILES deve ter a chave igual ao campo validado
$rules = [
    'foto' => ['label' => 'Foto de perfil', 'rules' => 'mimes:jpg,png,webp|max_file:2048'],
];

$errors = Validator::check($_FILES + $_POST, $rules);
```

### CPF e regex

```php
$rules = [
    'cpf'    => ['label' => 'CPF',    'rules' => 'required|cpf'],
    'codigo' => ['label' => 'Código', 'rules' => 'required|regex:/^FAB-\d{4}$/'],
];
```

### Seleção múltipla (array)

```php
$rules = [
    'categorias' => ['label' => 'Categorias', 'rules' => 'required|array|min:1|max:5'],
];
```

### Campo condicional (`sometimes`)

```php
// Só valida 'desconto' se ele estiver presente no payload (útil em PATCH/atualizações parciais)
$rules = [
    'desconto' => ['label' => 'Desconto', 'rules' => 'sometimes|float|min:0'],
];
```

---

## Mensagens de erro

As mensagens geradas pelo `Validator` seguem o padrão:

```
O campo "Nome do Campo" é obrigatório.
O campo "E-mail" não é um e-mail válido.
O campo "Senha" deve ter no mínimo 8 caracteres.
O campo "Senha" não confere com a confirmação.
O valor do campo "E-mail" já está em uso.
```

No contexto web (`make()`), o nome do campo é formatado com `<b>` nas mensagens HTML.

---

## Acesso aos erros na View

```php
// No controller, após Redirect::page(...)
// Na view, via helper exibeAlerta() ou diretamente:
$errors = Session::get('formErrors'); // array<campo, mensagem_html>
$inputs = Session::get('formInputs'); // array com os valores enviados
```
