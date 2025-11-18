<?php
// 1. INCLUI O HEADER DO ADMIN
require_once 'includes/header_admin.php';

try {
    // Busca os pedidos
    $query = "SELECT id, nome_cliente, total_pedido, data_pedido, status 
              FROM pedidos 
              WHERE id_usuario = :id_usuario 
              ORDER BY id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id_usuario' => $id_usuario_logado]);
    $pedidos = $stmt->fetchAll();

} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pedidos</h1>
</div>

<?php if (isset($_GET['status_update']) && $_GET['status_update'] == 'sucesso'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> Status do pedido atualizado com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-4">Nº Pedido</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Data</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pedidos): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo $pedido['id']; ?></td>
                                <td><?php echo htmlspecialchars($pedido['nome_cliente']); ?></td>
                                <td>R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                <td>
                                    <?php
                                    $status = $pedido['status'];
                                    $badgeClass = 'bg-secondary'; // Padrão
                                    
                                    if ($status == 'Aguardando Pagamento') $badgeClass = 'bg-warning text-dark';
                                    if ($status == 'Pago' || $status == 'Concluído') $badgeClass = 'bg-success';
                                    if ($status == 'Enviado' || $status == 'Em Separação') $badgeClass = 'bg-info text-dark';
                                    if ($status == 'Cancelado') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge rounded-pill <?php echo $badgeClass; ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="pedido_detalhe.php?id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        Ver Detalhes
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Nenhum pedido encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>