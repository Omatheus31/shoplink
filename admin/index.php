<?php
// 1. INCLUI O NOVO HEADER DO ADMIN
// Ele já faz o 'verifica_login.php' e conecta ao banco
require_once 'includes/header_admin.php';

try {
    // 2. BUSCAR AS ESTATÍSTICAS (Mesma lógica de antes)
    
    // Pedidos Pendentes
    $sql_pedidos = "SELECT COUNT(*) FROM pedidos WHERE id_usuario = :id_usuario AND status = 'Aguardando Pagamento'";
    $stmt_pedidos = $pdo->prepare($sql_pedidos);
    $stmt_pedidos->execute([':id_usuario' => $id_usuario_logado]);
    $pedidos_pendentes = $stmt_pedidos->fetchColumn();

    // Total de Produtos
    $sql_produtos = "SELECT COUNT(*) FROM produtos WHERE id_usuario = :id_usuario";
    $stmt_produtos = $pdo->prepare($sql_produtos);
    $stmt_produtos->execute([':id_usuario' => $id_usuario_logado]);
    $total_produtos = $stmt_produtos->fetchColumn();

    // Total de Categorias
    $sql_categorias = "SELECT COUNT(*) FROM categorias WHERE id_usuario = :id_usuario";
    $stmt_categorias = $pdo->prepare($sql_categorias);
    $stmt_categorias->execute([':id_usuario' => $id_usuario_logado]);
    $total_categorias = $stmt_categorias->fetchColumn();

} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Erro ao buscar dados: ' . $e->getMessage() . '</div>';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row g-4">
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 border-start border-4 border-warning">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-1">Aguardando Pagamento</h6>
                    <h2 class="mb-0 display-6 fw-bold text-dark"><?php echo $pedidos_pendentes; ?></h2>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 p-3 rounded">
                    <i class="bi bi-clock-history fs-1 text-warning"></i>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <a href="pedidos.php" class="text-decoration-none text-warning fw-bold small">
                    Ver pedidos <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 border-start border-4 border-primary">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-1">Produtos Cadastrados</h6>
                    <h2 class="mb-0 display-6 fw-bold text-dark"><?php echo $total_produtos; ?></h2>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 p-3 rounded">
                    <i class="bi bi-box-seam fs-1 text-primary"></i>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <a href="produtos.php" class="text-decoration-none text-primary fw-bold small">
                    Gerenciar produtos <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 border-start border-4 border-success">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-1">Categorias Ativas</h6>
                    <h2 class="mb-0 display-6 fw-bold text-dark"><?php echo $total_categorias; ?></h2>
                </div>
                <div class="icon-box bg-success bg-opacity-10 p-3 rounded">
                    <i class="bi bi-tags fs-1 text-success"></i>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <a href="categorias.php" class="text-decoration-none text-success fw-bold small">
                    Gerenciar categorias <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

</div>

<?php
// INCLUI O RODAPÉ DO ADMIN
require_once 'includes/footer_admin.php';
?>