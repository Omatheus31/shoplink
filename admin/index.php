<?php
$titulo_pagina = "Dashboard";
require_once 'includes/header_admin.php';

try {
    // 1. Total de Pedidos
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos");
    $total_pedidos = $stmt->fetchColumn();

    // 2. Pedidos Aguardando Pagamento
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'Aguardando Pagamento'");
    $aguardando_pagamento = $stmt->fetchColumn();

    // 3. Total de Produtos
    $stmt = $pdo->query("SELECT COUNT(*) FROM produtos");
    $total_produtos = $stmt->fetchColumn();
    
    // 4. Total de Categorias
    $stmt = $pdo->query("SELECT COUNT(*) FROM categorias");
    $total_categorias = $stmt->fetchColumn();

} catch (PDOException $e) {
    echo "Erro ao carregar dados: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-white bg-primary h-100 shadow-sm">
            <div class="card-header">Total Pedidos</div>
            <div class="card-body">
                <h1 class="card-title"><?php echo $total_pedidos; ?></h1>
                <p class="card-text">Pedidos realizados na loja.</p>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="pedidos.php" class="text-white text-decoration-none stretched-link">Ver detalhes <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card text-dark bg-warning h-100 shadow-sm">
            <div class="card-header">Aguardando Pagamento</div>
            <div class="card-body">
                <h1 class="card-title"><?php echo $aguardando_pagamento; ?></h1>
                <p class="card-text">Precisam de atenção.</p>
            </div>
             <div class="card-footer bg-transparent border-0">
                <a href="pedidos.php" class="text-dark text-decoration-none stretched-link">Ver pedidos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card text-white bg-success h-100 shadow-sm">
            <div class="card-header">Produtos Ativos</div>
            <div class="card-body">
                <h1 class="card-title"><?php echo $total_produtos; ?></h1>
                <p class="card-text">Itens no catálogo.</p>
            </div>
             <div class="card-footer bg-transparent border-0">
                <a href="produtos.php" class="text-white text-decoration-none stretched-link">Gerenciar <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-dark bg-light border h-100 shadow-sm">
            <div class="card-header">Categorias</div>
            <div class="card-body">
                <h1 class="card-title"><?php echo $total_categorias; ?></h1>
                <p class="card-text">Departamentos.</p>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="categorias.php" class="text-dark text-decoration-none stretched-link">Gerenciar <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>