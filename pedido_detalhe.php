<?php
// pedido_detalhe.php (Raiz - Área do Cliente)
require_once 'config/database.php';

// 1. DEFINE TÍTULO E INCLUI O HEADER
// O header_public.php já contém o session_start(), então não precisamos chamar aqui.
$titulo_pagina = 'Detalhes do Pedido';
require_once 'includes/header_public.php';

// 2. VERIFICA LOGIN
// Agora que o header iniciou a sessão, podemos verificar se existe o id_usuario
if (!isset($_SESSION['id_usuario'])) {
    // Usamos JavaScript para redirecionar porque o header já enviou HTML
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

// 3. VALIDA ID DO PEDIDO
if (!isset($_GET['id'])) {
    echo "<script>window.location.href='meus_pedidos.php';</script>";
    exit();
}

$id_pedido = (int)$_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

try {
    // 4. BUSCA O PEDIDO (Com trava de segurança: só se for dono do pedido)
    $sql = "SELECT * FROM pedidos WHERE id = :id AND id_usuario = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_pedido, ':user_id' => $id_usuario]);
    $pedido = $stmt->fetch();

    if (!$pedido) {
        echo "<script>alert('Pedido não encontrado.'); window.location='meus_pedidos.php';</script>";
        exit();
    }

    // 5. BUSCA OS ITENS DO PEDIDO
    $sql_itens = "SELECT pi.*, p.nome, p.imagem_url 
                  FROM pedido_itens pi
                  LEFT JOIN produtos p ON pi.id_produto = p.id
                  WHERE pi.id_pedido = :id_pedido";
    $stmt_itens = $pdo->prepare($sql_itens);
    $stmt_itens->execute([':id_pedido' => $id_pedido]);
    $itens = $stmt_itens->fetchAll();

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <div>
        <a href="meus_pedidos.php" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <h2 class="fw-bold mt-1">Pedido #<?php echo $pedido['id']; ?></h2>
    </div>
    
    <?php 
    $st = $pedido['status'];
    $bg = match($st) {
        'Aguardando Pagamento' => 'bg-warning text-dark',
        'Pago', 'Concluído' => 'bg-success',
        'Cancelado' => 'bg-danger',
        default => 'bg-secondary'
    };
    ?>
    <span class="badge <?php echo $bg; ?> fs-6 px-3 py-2 rounded-pill"><?php echo $st; ?></span>
</div>

<div class="row g-4">
    
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Itens Comprados</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Produto</th>
                            <th>Qtd</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="uploads/<?php echo htmlspecialchars($item['imagem_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['nome']); ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover;" 
                                             class="rounded me-3 border">
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($item['nome']); ?></h6>
                                            <small class="text-muted">Unit: R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $item['quantidade']; ?></td>
                                <td class="fw-bold">
                                    R$ <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="2" class="text-end pe-4 fw-bold">TOTAL DO PEDIDO:</td>
                            <td class="fw-bold text-success fs-5">
                                R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        
        <?php if ($pedido['status'] == 'Aguardando Pagamento'): ?>
            <div class="card shadow-sm border-0 mb-3 border-warning">
                <div class="card-body text-center">
                    <h6 class="text-warning fw-bold"><i class="bi bi-exclamation-circle"></i> Pagamento Pendente</h6>
                    <p class="small text-muted mb-3">Finalize o pagamento para enviarmos seu produto.</p>
                    <a href="pagamento.php?id_pedido=<?php echo $pedido['id']; ?>" class="btn btn-warning w-100 fw-bold">
                        Pagar Agora
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-geo-alt"></i> Entrega</h6>
            </div>
            <div class="card-body">
                <p class="mb-1 fw-bold"><?php echo htmlspecialchars($pedido['nome_cliente']); ?></p>
                <p class="mb-0 text-muted small"><?php echo htmlspecialchars($pedido['endereco_cliente']); ?></p>
                <hr>
                <small class="text-muted">Telefone: <?php echo htmlspecialchars($pedido['telefone_cliente']); ?></small>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card"></i> Pagamento</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Método:</span>
                    <span class="fw-bold"><?php echo htmlspecialchars($pedido['metodo_pagamento'] ?? 'PIX'); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="text-muted">Data:</span>
                    <span><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></span>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer_public.php'; ?>