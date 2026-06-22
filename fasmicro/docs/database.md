# Banco de Dados

## Criação do Banco

Execute o script SQL incluído na raiz do projeto:

```bash
mysql -u root -p < script-database.sql
```

O script cria o banco `fasmicro` e todas as tabelas necessárias.

---

## Tabelas

| Tabela | Descrição |
|---|---|
| `usuario` | Usuários do sistema com nível de acesso |
| `categoria` | Categorias de produtos |
| `unidademedida` | Unidades de medida (sigla + descrição) |
| `produto` | Produtos vinculados a categoria e unidade de medida |
| `usuariorecuperasenha` | Tokens para recuperação de senha |

---

## Relacionamentos

```
categoria        (1) ──── (N) produto
unidademedida    (1) ──── (N) produto
usuario          (1) ──── (N) usuariorecuperasenha
```

---

## Papéis de Usuário

O sistema usa um modelo numérico onde **menor número = maior privilégio**.

| Nível | Papel | Acesso web | Acesso API |
|---|---|---|---|
| 1 | Super Administrador | Total | Total |
| 11 | Administrador | CRUD completo | CRUD completo |
| 21 | Usuário comum | Apenas leitura | Apenas GET |

O Super Administrador inicial é criado a partir das variáveis `SUPER_USER_EMAIL` e `SUPER_USER_PASS` definidas no `.env`.
