<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System - Home</title>
    <link rel="stylesheet" href="/assests/bootstrap/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-primary fw-bold" href="/">
                <i class="bi bi-bar-chart-line-fill text-primary"></i> ERP System
            </a>

            <div class="collapse navbar-collapse justify-content-center">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/venda">
                            <i class="bi bi-cart3"></i> Vendas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/produto">
                            <i class="bi bi-box-seam"></i> Produtos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/recebimento">
                            <i class="bi bi-currency-dollar"></i> Recebimentos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pagamento">
                            <i class="bi bi-credit-card"></i> Pagamentos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/tipoDocumento">
                            <i class="bi bi-file-earmark"></i> Tipo Documento
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pessoa">
                            <i class="bi bi-people"></i>Pessoas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/usuario">
                            <i class="bi bi-person"></i> Usuários
                        </a>
                    </li>
                </ul>
            </div>

            <div class="right-actions">
                <div class="user-profile">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span>Nome</span>
                </div>
                <a class="btn btn-danger" href="/">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <main>
        <?= $content ?>
    </main>

    <script src="/assests/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>