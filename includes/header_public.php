<?php
// includes/header_public.php

// 1. Inicia sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Lógica do Nome da Loja Dinâmico
$nome_loja_exibicao = "Shoplink"; // Valor padrão (Fallback)

if (isset($pdo)) {
    try {
        $stmt_conf = $pdo->query("SELECT valor FROM configuracoes WHERE chave = 'nome_loja'");
        $resultado_nome = $stmt_conf->fetchColumn();
        if ($resultado_nome) {
            $nome_loja_exibicao = $resultado_nome;
        }
    } catch (Exception $e) {
        // Se der erro no banco, mantém o padrão silenciosamente
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina . ' - ' : ''; ?><?php echo htmlspecialchars($nome_loja_exibicao); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> 
    
    <style>
         body { background-color: #f8f9fa; }
         .navbar-brand i { margin-right: 5px; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    
    <header class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-shop"></i> <?php echo htmlspecialchars($nome_loja_exibicao); ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-journal-bookmark-fill"></i> Catálogo
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="carrinho.php">
                            <i class="bi bi-cart-fill"></i> Carrinho 
                            <span id="cart-counter" class="badge bg-success rounded-pill ms-1">0</span>
                        </a>
                    </li>

                    <?php if (isset($_SESSION['id_usuario'])): ?>
                        
                        <li class="nav-item border-start ms-2 ps-2 d-none d-lg-block"></li> <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item ms-2">
                                <a class="btn btn-danger btn-sm fw-bold" href="admin/index.php">
                                    <i class="bi bi-speedometer2"></i> Painel Admin
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['nome'] ?? 'Minha Conta'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="minha_conta.php"><i class="bi bi-person-gear"></i> Meus Dados</a></li>
                                <li><a class="dropdown-item" href="meus_pedidos.php"><i class="bi bi-receipt"></i> Meus Pedidos</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                            </ul>
                        </li>

                    <?php else: ?>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-light btn-sm" href="login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Entrar / Cadastrar
                            </a>
                        </li>
                    <?php endif; ?>
                    
                </ul>
            </div>
        </div>
    </header>

    <main class="container mt-4 flex-grow-1">