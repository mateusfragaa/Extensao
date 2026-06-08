# Rotas da Aplicação Web

As rotas web seguem o padrão `/{Controller}/{metodo}/{parametro}`. O roteador (`Routes`) mapeia a URL para o controller e método correspondentes, instanciando-os automaticamente.

> **Proteção CSRF:** todas as rotas que recebem `POST`, `PUT`, `PATCH` ou `DELETE` são automaticamente protegidas contra CSRF pelo `ControllerMain`. Formulários devem incluir `<?= csrfField() ?>` e requisições AJAX devem enviar o header `X-CSRF-Token`. Consulte [docs/security.md](security.md) para detalhes.

---

## Rotas Públicas

Acessíveis sem autenticação.

| Rota | Controller | Método | Descrição |
|---|---|---|---|
| `/` | Home | index | Página inicial |
| `/Home/contato` | Home | contato | Formulário de contato (GET/POST) |
| `/Login` | Login | index | Tela de login |
| `/Login/entrar` | Login | entrar | Processar login (POST) |
| `/Login/sair` | Login | sair | Logout |

---

## Rotas Administrativas

Exigem autenticação. Usuários não autenticados são redirecionados para `/Login`.

### Produtos

| Rota | Descrição |
|---|---|
| `/Produto` | Listagem de produtos |
| `/Produto/form` | Cadastrar novo produto |
| `/Produto/form/{id}` | Editar produto |
| `/Produto/excluir/{id}` | Excluir produto |

### Unidades de Medida

| Rota | Descrição |
|---|---|
| `/UnidadeMedida` | Listagem de unidades |
| `/UnidadeMedida/form` | Cadastrar unidade |
| `/UnidadeMedida/form/{id}` | Editar unidade |
| `/UnidadeMedida/excluir/{id}` | Excluir unidade |

### Categorias

| Rota | Descrição |
|---|---|
| `/Categoria` | Listagem de categorias |
| `/Categoria/form` | Cadastrar categoria |
| `/Categoria/form/{id}` | Editar categoria |
| `/Categoria/excluir/{id}` | Excluir categoria |

---

## Rotas da API REST

As rotas da API seguem o padrão `/api/v1/{recurso}` e são registradas em `app/Config/ApiRoutesConfig.php`.

Consulte a [referência completa de endpoints](api/endpoints.md) para detalhes de cada rota.
