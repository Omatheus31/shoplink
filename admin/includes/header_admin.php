<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se é admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$nome_admin = $_SESSION['nome'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina . ' - ' : ''; ?>Painel Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Estilos para o Sidebar Responsivo */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100; /* Atrás do navbar */
            padding: 48px 0 0; /* Altura da navbar */
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: static; /* No mobile, ele flui com a página */
                height: auto;
                padding-top: 0;
                display: none; /* Escondido por padrão */
            }
            
            .sidebar.show {
                display: block; /* Mostra quando a classe 'show' é adicionada pelo JS do Bootstrap */
            }
        }

        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }

        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }
    </style>
</head>
<body>

<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="index.php">Shoplink Admin</a>
  
  <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="w-100 d-flex justify-content-end align-items-center px-3">
      <span class="text-light me-3 d-none d-sm-inline">Olá, <?php echo htmlspecialchars($nome_admin); ?></span>
      <div class="navbar-nav">
        <div class="nav-item text-nowrap">
          <a class="nav-link px-3 text-danger fw-bold" href="../logout.php">Sair <i class="bi bi-box-arrow-right"></i></a>
        </div>
      </div>
  </div>
</header>

<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link <?php echo ($titulo_pagina == 'Dashboard') ? 'active fw-bold' : 'text-dark'; ?>" href="index.php">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($titulo_pagina == 'Gestão de Pedidos') ? 'active fw-bold' : 'text-dark'; ?>" href="pedidos.php">
              <i class="bi bi-file-earmark-text me-2"></i> Pedidos
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($titulo_pagina == 'Produtos') ? 'active fw-bold' : 'text-dark'; ?>" href="produtos.php">
              <i class="bi bi-box-seam me-2"></i> Produtos
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($titulo_pagina == 'Categorias') ? 'active fw-bold' : 'text-dark'; ?>" href="categorias.php">
              <i class="bi bi-tags me-2"></i> Categorias
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($titulo_pagina == 'Clientes') ? 'active fw-bold' : 'text-dark'; ?>" href="clientes.php">
              <i class="bi bi-people me-2"></i> Clientes
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($titulo_pagina == 'Configurações') ? 'active fw-bold' : 'text-dark'; ?>" href="configuracoes.php">
              <i class="bi bi-gear me-2"></i> Configurações
            </a>
          </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
          <span>Atalhos</span>
        </h6>
        <ul class="nav flex-column mb-2">
          <li class="nav-item">
            <a class="nav-link text-primary" href="../index.php" target="_blank">
              <i class="bi bi-shop me-2"></i> Ver Loja Online
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">