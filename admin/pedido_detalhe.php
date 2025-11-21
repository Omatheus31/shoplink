<?php
// admin/pedido_detalhe.php
$titulo_pagina = "Detalhes do Pedido"; 
require_once 'includes/header_admin.php';

if (!isset($_GET['id'])) { header("Location: pedidos.php"); exit(); }
$id_pedido = (int)$_GET['id'];

// --- 1. LÓGICA PARA ATUALIZAR STATUS (Embedada aqui mesmo) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_status'])) {
    $novo_status = $_POST['novo_status'];
    try {
        $stmt_up = $pdo->prepare("UPDATE pedidos SET status = :status WHERE id = :id");
        $stmt_up->execute([':status' => $novo_status, ':id' => $id_pedido]);
        // Redireciona para evitar reenvio de form (PRG Pattern)
        echo "<script>window.location.href='pedido_detalhe.php?id=$id_pedido&status_update=sucesso';</script>";
        exit;
    } catch (PDOException $e) {
        $erro_msg = "Erro ao atualizar: " . $e->getMessage();
    }
}

// --- 2. BUSCA O PEDIDO ---
$sql_pedido = "SELECT * FROM pedidos WHERE id = :id";
$stmt_pedido = $pdo->prepare($sql_pedido);
$stmt_pedido->execute([':id' => $id_pedido]);
$pedido = $stmt_pedido->fetch();

if (!$pedido) { header("Location: pedidos.php"); exit(); }

// --- 3. BUSCA OS ITENS ---
$sql_itens = "SELECT pi.*, p.nome as nome_produto, p.imagem_url 
              FROM pedido_itens pi
              LEFT JOIN produtos p ON pi.id_produto = p.id
              WHERE pi.id_pedido = :id";
$stmt_itens = $pdo->prepare($sql_itens);
$stmt_itens->execute([':id' => $id_pedido]);
$itens = $stmt_itens->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pedido #<?php echo $pedido['id']; ?></h1>
    <a href="pedidos.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<?php if (isset($_GET['status_update'])): ?>
    <div class="alert alert-success">Status atualizado com sucesso!</div>
<?php endif; ?>

<div class="row g-4">
    
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Itens do Pedido</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Produto</th>
                            <th>Qtd</th>
                            <th>Unitário</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php if($item['imagem_url']): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($item['imagem_url']); ?>" width="40" height="40" class="rounded me-2">
                                        <?php endif; ?>
                                        <strong><?php echo htmlspecialchars($item['nome_produto'] ?? 'Produto Removido'); ?></strong>
                                    </div>
                                </td>
                                <td><?php echo $item['quantidade']; ?></td>
                                <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-end p-3">
                <h5 class="mb-0">Total: <span class="text-success fw-bold">R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?></span></h5>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Gerenciar Status</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <label class="form-label text-muted small">Status Atual</label>
                    <select class="form-select mb-3" name="novo_status">
                        <?php 
                        $statuses = ['Aguardando Pagamento', 'Pago', 'Em Separação', 'Enviado', 'Concluído', 'Cancelado'];
                        foreach($statuses as $st) {
                            $selected = ($pedido['status'] == $st) ? 'selected' : '';
                            echo "<option value='$st' $selected>$st</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Atualizar Status</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Dados do Cliente</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Nome:</strong> <?php echo htmlspecialchars($pedido['nome_cliente']); ?></p>
                <p class="mb-1"><strong>Telefone:</strong> <?php echo htmlspecialchars($pedido['telefone_cliente']); ?></p>
                <p class="mb-3"><strong>Email:</strong> (Buscar no cadastro se necessário)</p>
                
                <h6 class="text-muted small border-top pt-3">Entrega</h6>
                <p class="small mb-0"><?php echo htmlspecialchars($pedido['endereco_cliente']); ?></p>
                
                <h6 class="text-muted small border-top pt-3 mt-3">Pagamento</h6>
                <span class="badge bg-dark"><?php echo htmlspecialchars($pedido['metodo_pagamento'] ?? 'Não informado'); ?></span>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>