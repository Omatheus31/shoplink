<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-100"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="assets/css/style.css"> 
    
    <style>
         body { background-color: #f8f9fa; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-shop"></i> Shoplink
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="carrinho.php">
                            <i class="bi bi-cart-fill"></i> Carrinho (<span id="cart-counter">0</span>)
                        </a>
                    </li>
                    <?php if (isset($_SESSION['id_usuario'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="minha_conta.php"><i class="bi bi-person-fill"></i> Minha Conta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-sair" href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login / Cadastrar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>
    <main class="container mt-4 flex-grow-1">