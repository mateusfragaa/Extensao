<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System - Home</title>
    <link rel="stylesheet" href="/assests/bootstrap/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assests/css/geral.css">
    <link rel="stylesheet" href="/assests/css/main.css">
    <link rel="stylesheet" href="/assests/css/venda.css">
    <link rel="stylesheet" href="/assests/css/produto.css">
    <link rel="stylesheet" href="/assests/css/recebimento.css">
    <link rel="stylesheet" href="/assests/css/pagamento.css">
    <link rel="stylesheet" href="/assests/css/tipoDocumento.css">
    <link rel="stylesheet" href="/assests/css/pessoa.css">
    <link rel="stylesheet" href="/assests/css/usuario.css">
    <link rel="stylesheet" href="/assests/css/forms/formVenda.css">
    <link rel="stylesheet" href="/assests/css/forms/formRecebimento.css">
    <link rel="stylesheet" href="/assests/css/forms/formPagamento.css">
    <link rel="stylesheet" href="/assests/css/forms/formTipoDocumento.css">
    <link rel="stylesheet" href="/assests/css/forms/formPessoa.css">
    <link rel="stylesheet" href="/assests/css/forms/formUsuario.css">
    <style>
        /* Hamburger toggler visível em telas pequenas */
        .navbar-toggler {
            border-color: rgba(0,0,0,.2);
        }
        /* Em mobile: empilha nome e botão Sair dentro do collapse */
        @media (max-width: 991.98px) {
            .right-actions {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.5rem 0;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-primary fw-bold" href="/homeSistema/">
                <i class="bi bi-bar-chart-line-fill text-primary"></i> ERP System
            </a>

            <!-- Botão hamburguer — aparece em telas < lg -->
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarMenu"
                    aria-controls="navbarMenu"
                    aria-expanded="false"
                    aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarMenu">
                <ul class="navbar-nav">
                    <?php if (temPermissao('Venda')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/venda">
                                <i class="bi bi-cart3"></i> Vendas
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (temPermissao('Produto')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/produto">
                                <i class="bi bi-box-seam"></i> Produtos
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (temPermissao('Recebimento')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/recebimento">
                                <i class="bi bi-currency-dollar"></i> Recebimentos
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (temPermissao('Pagamento')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/pagamento">
                                <i class="bi bi-credit-card"></i> Pagamentos
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/tipoDocumento">
                            <i class="bi bi-file-earmark"></i> Tipo Documento
                        </a>
                    </li>

                    <?php if (temPermissao('Pessoa')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/pessoa">
                                <i class="bi bi-people"></i>Pessoas
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (temPermissao('Usuario')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/usuario">
                                <i class="bi bi-person"></i> Usuários
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <!--mobaile  -->
                <div class="right-actions d-lg-none mt-2">
                    <div class="user-profile">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span>Nome</span>
                    </div>
                    <a class="btn btn-danger btn-sm" href="/">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </a>
                </div>
            </div>

            <!-- desktop -->
            <div class="right-actions d-none d-lg-flex">
                <div class="user-profile">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span><?= \Core\Library\Session::get('usuario_logado')['USU_NOME'] ?? '' ?></span>
                </div>
                <a class="btn btn-danger" href="/auth/logout">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <main>
        <?= $content ?>
    </main>

    <script src="/assests/bootstrap/js/teste.js"></script>
    <script src="/assests/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
