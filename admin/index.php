<?php
$titulo_pagina = "Dashboard";
require_once 'includes/header_admin.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos");
    $total_pedidos = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'Aguardando Pagamento'");
    $aguardando_pagamento = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM produtos");
    $total_produtos = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM categorias");
    $total_categorias = $stmt->fetchColumn();

} catch (PDOException $e) {
    echo "Erro ao carregar dados: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Visão Geral</h1>
</div>

<div class="row g-4">
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-bold">Total Pedidos</h6>
                    <h2 class="mb-0 fw-bold text-primary"><?php echo $total_pedidos; ?></h2>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                    <i class="bi bi-cart-check fs-4"></i>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-2 px-4">
                <a href="pedidos.php" class="text-decoration-none small fw-bold text-primary">
                    Ver detalhes <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-bold">Pagamento Pendente</h6>
                    <h2 class="mb-0 fw-bold text-warning"><?php echo $aguardando_pagamento; ?></h2>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-2 px-4">
                <a href="pedidos.php" class="text-decoration-none small fw-bold text-warning">
                    Ver lista <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-bold">Produtos Ativos</h6>
                    <h2 class="mb-0 fw-bold text-success"><?php echo $total_produtos; ?></h2>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle p-3">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-2 px-4">
                <a href="produtos.php" class="text-decoration-none small fw-bold text-success">
                    Gerenciar <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-bold">Categorias</h6>
                    <h2 class="mb-0 fw-bold text-dark"><?php echo $total_categorias; ?></h2>
                </div>
                <div class="icon-box bg-dark bg-opacity-10 text-dark rounded-circle p-3">
                    <i class="bi bi-tags fs-4"></i>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-2 px-4">
                <a href="categorias.php" class="text-decoration-none small fw-bold text-dark">
                    Gerenciar <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <div class="alert alert-info shadow-sm border-0">
        <i class="bi bi-info-circle-fill me-2"></i>
        <strong>Dica:</strong> Use o menu lateral para navegar entre as seções de administração.
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>