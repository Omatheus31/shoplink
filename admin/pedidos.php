<?php
// admin/pedidos.php
$titulo_pagina = "Gestão de Pedidos";
require_once 'includes/header_admin.php';

try {
    // BUSCA TODOS OS PEDIDOS (Sem filtro de usuário, pois ADMIN vê tudo)
    $query = "SELECT id, nome_cliente, total_pedido, data_pedido, status, metodo_pagamento 
              FROM pedidos 
              ORDER BY id DESC";
    $stmt = $pdo->query($query);
    $pedidos = $stmt->fetchAll();

} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestão de Pedidos</h1>
</div>

<?php if (isset($_GET['status_update']) && $_GET['status_update'] == 'sucesso'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> Status atualizado com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#ID</th>
                        <th>Cliente</th>
                        <th>Pagamento</th>
                        <th>Total</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pedidos): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo $pedido['id']; ?></td>
                                <td><?php echo htmlspecialchars($pedido['nome_cliente']); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?php echo htmlspecialchars($pedido['metodo_pagamento'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?></td>
                                <td><?php echo date('d/m/y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                <td>
                                    <?php
                                    $status = $pedido['status'];
                                    $badgeClass = match($status) {
                                        'Aguardando Pagamento' => 'bg-warning text-dark',
                                        'Pago', 'Concluído' => 'bg-success',
                                        'Em Separação', 'Enviado' => 'bg-info text-dark',
                                        'Cancelado' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge rounded-pill <?php echo $badgeClass; ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="pedido_detalhe.php?id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-primary">
                                        Ver Detalhes
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Nenhum pedido realizado ainda.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>