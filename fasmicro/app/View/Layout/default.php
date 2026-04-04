<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Comunitário - Gestão Simples e Social</title>
    <link rel="stylesheet" href="/assests/bootstrap/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assests/css/home.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home"><i class="bi bi bi-bar-chart-line-fill me-2"></i>ERP System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link me-3" href="#sobre">Sobre o Projeto</a></li>
                    <li class="nav-item"><a class="nav-link me-3" href="#funcionalidades">Funcionalidades</a></li>
                    <li class="nav-item"><a class="nav-link me-3" href="#contato">Contato</a></li>
                    <li class="nav-item"><a class="nav-link me-2" href="#sobre">Saiba Mais</a></li>
                    <li class="nav-item"><a class="btn btn-outline-primary btn-lg" href="auth/formLogin">Entrar</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <?= $content ?>
    </main>
    <script src="/assests/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>