<?php
// meus_pedidos.php
require_once 'config/database.php';
$titulo_pagina = 'Meus Pedidos';
require_once 'includes/header_public.php';

// Verifica Login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php?erro=acesso_negado');
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

try {
    // Busca pedidos deste usuário com a nova coluna metodo_pagamento
    $sql = "SELECT * FROM pedidos WHERE id_usuario = :id ORDER BY id DESC";
    $stmt = $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $pedidos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Histórico de Pedidos</h2>
    <a href="index.php" class="btn btn-outline-secondary">Continuar Comprando</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#ID</th>
                        <th>Data</th>
                        <th>Pagamento</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pedidos): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo $pedido['id']; ?></td>
                                
                                <td><?php echo date('d/m/Y \à\s H:i', strtotime($pedido['data_pedido'])); ?></td>
                                
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?php echo htmlspecialchars($pedido['metodo_pagamento'] ?? 'PIX'); ?>
                                    </span>
                                </td>

                                <td class="fw-bold text-dark">
                                    R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?>
                                </td>
                                
                                <td>
                                    <?php 
                                    $st = $pedido['status'];
                                    $bg = match($st) {
                                        'Aguardando Pagamento' => 'bg-warning text-dark',
                                        'Pago', 'Concluído' => 'bg-success',
                                        'Enviado', 'Em Separação' => 'bg-info text-dark',
                                        'Cancelado' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge rounded-pill <?php echo $bg; ?>"><?php echo $st; ?></span>
                                </td>
                                
                                <td class="text-end pe-4">
                                    <a href="pedido_detalhe.php?id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye-fill"></i> Detalhes
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-bag-x fs-1 d-block mb-3"></i>
                                    Você ainda não realizou nenhum pedido.
                                </div>
                                <a href="index.php" class="btn btn-primary mt-2">Ir para a Loja</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_public.php'; ?>