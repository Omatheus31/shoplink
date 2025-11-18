<?php
// 1. O GUARDIÃO!
// Este header agora protege todas as páginas que o incluírem.
require_once dirname(__DIR__) . '/verifica_login.php'; // Caminho absoluto seguro

// 2. CONEXÃO COM O BANCO (A CORREÇÃO ESTÁ AQUI)
// Usamos dirname(__DIR__, 2) para voltar duas pastas (de admin/includes para a raiz)
require_once dirname(__DIR__, 2) . '/config/database.php';

// 3. BUSCA O NOME DA PÁGINA ATUAL para o 'active'
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina ?? 'Admin'; ?> - Shoplink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css?v=2.0"> 
    
    <style>
         body { background-color: #f8f9fa; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    
    <header class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4"> <a class="navbar-brand" href="index.php">
                <i class="bi bi-shield-lock-fill"></i> Painel Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($pagina_atual == 'index.php') ? 'active' : ''; ?>" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($pagina_atual == 'pedidos.php' || $pagina_atual == 'pedido_detalhe.php') ? 'active' : ''; ?>" href="pedidos.php">
                            <i class="bi bi-receipt"></i> Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($pagina_atual, 'produto') !== false) ? 'active' : ''; ?>" href="produtos.php">
                            <i class="bi bi-box-seam-fill"></i> Produtos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($pagina_atual, 'categoria') !== false) ? 'active' : ''; ?>" href="categorias.php">
                            <i class="bi bi-tags-fill"></i> Categorias
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="navbar-text me-3 text-light">
                            Olá, <strong><?php echo htmlspecialchars($nome_loja_logado); ?></strong>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger btn-sm text-white px-3" href="../logout.php">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    
    <main class="container-fluid px-4 my-4 flex-grow-1">