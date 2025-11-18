<?php
// 1. INCLUI O HEADER DO ADMIN
$titulo_pagina = "Detalhes do Pedido"; 
require_once 'includes/header_admin.php';

// 2. Verifica se o ID do pedido foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pedidos.php");
    exit();
}
$id_pedido = (int)$_GET['id'];

try {
    // 3. BUSCA OS DADOS DO PEDIDO (com lógica de 3 papéis)
    $sql_pedido = "SELECT p.*, u.nome_loja as nome_cliente_loja 
                   FROM pedidos p 
                   JOIN usuarios u ON p.id_usuario = u.id 
                   WHERE p.id = :id_pedido";
    $params_pedido = [':id_pedido' => $id_pedido];

    // Admin Loja SÓ PODE ver o seu
    if ($_SESSION['role'] === 'admin_loja') {
        $sql_pedido .= " AND p.id_usuario = :id_usuario";
        $params_pedido[':id_usuario'] = $id_usuario_logado;
    }
    
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute($params_pedido);
    $pedido = $stmt_pedido->fetch();

    if (!$pedido) {
        header("Location: pedidos.php");
        exit();
    }

    // 4. BUSCA OS ITENS DO PEDIDO
    $sql_itens = "SELECT pi.*, p.nome as nome_produto 
                  FROM pedido_itens pi
                  LEFT JOIN produtos p ON pi.id_produto = p.id
                  WHERE pi.id_pedido = :id_pedido";
    $stmt_itens = $pdo->prepare($sql_itens);
    $stmt_itens->execute([':id_pedido' => $id_pedido]);
    $itens_pedido = $stmt_itens->fetchAll();

} catch (PDOException $e) {
    die("Erro ao buscar dados do pedido: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detalhes do Pedido #<?php echo $pedido['id']; ?></h1>
    <a href="pedidos.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left-circle-fill"></i> Voltar para Pedidos
    </a>
</div>

<div class="row g-4">
    <!-- Coluna da Esquerda: Itens e Status -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h3 class="h5 mb-0"><i class="bi bi-basket-fill"></i> Itens do Pedido</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class.="ps-4">Produto</th>
                                <th scope="col">Qtd.</th>
                                <th scope="col">Preço Unit.</th>
                                <th scope="col">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens_pedido as $item): ?>
                                <tr>
                                    <td class="ps-4">
                                        <?php if ($item['nome_produto']): ?>
                                            <strong><?php echo htmlspecialchars($item['nome_produto']); ?></strong>
                                        <?php else: ?>
                                            <span class="text-danger fst-italic">[Produto Removido]</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $item['quantidade']; ?></td>
                                    <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                                    <td>R$ <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-end p-3">
                <h4 class="h5 mb-0">TOTAL: 
                    <span class="fw-bold text-success">
                        R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?>
                    </span>
                </h4>
            </div>
        </div>
    </div>

    <!-- Coluna da Direita: Cliente e Ações -->
    <div class="col-lg-4">
        <!-- Card de Ações -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h3 class="h5 mb-0"><i class="bi bi-gear-fill"></i> Ações do Pedido</h3>
            </div>
            <div class="card-body">
                <form class="status-form" action="atualizar_status_pedido.php" method="POST">
                    <input type="hidden" name="id_pedido" value="<?php echo $pedido['id']; ?>">
                    <div class="form-floating">
                        <select class="form-select" id="novo_status" name="novo_status">
                            <option value="Aguardando Pagamento" <?php echo ($pedido['status'] == 'Aguardando Pagamento') ? 'selected' : ''; ?>>
                                Aguardando Pagamento
                            </option>
                            <option value="Pago" <?php echo ($pedido['status'] == 'Pago') ? 'selected' : ''; ?>>
                                Pago
                            </option>
                            <option value="Em Separação" <?php echo ($pedido['status'] == 'Em Separação') ? 'selected' : ''; ?>>
                                Em Separação
                            </option>
                            <option value="Enviado" <?php echo ($pedido['status'] == 'Enviado') ? 'selected' : ''; ?>>
                                Enviado
                            </option>
                            <option value="Concluído" <?php echo ($pedido['status'] == 'Concluído') ? 'selected' : ''; ?>>
                                Concluído
                            </option>
                            <option value="Cancelado" <?php echo ($pedido['status'] == 'Cancelado') ? 'selected' : ''; ?>>
                                Cancelado
                            </option>
                        </select>
                        <label for="novo_status">Alterar Status do Pedido</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-check-circle-fill"></i> Atualizar Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Card de Cliente -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h3 class="h5 mb-0"><i class="bi bi-person-lines-fill"></i> Dados do Cliente</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Nome:</strong><br>
                        <?php echo htmlspecialchars($pedido['nome_cliente']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Telefone:</strong><br>
                        <?php echo htmlspecialchars($pedido['telefone_cliente']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Endereço:</strong><br>
                        <?php echo htmlspecialchars($pedido['endereco_cliente']); ?>
                    </li>
                    <!-- Se for Admin Master, mostra de qual loja é este cliente -->
                    <?php if ($_SESSION['role'] === 'admin_master'): ?>
                        <li class="list-group-item">
                            <strong>Loja do Cliente:</strong><br>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($pedido['nome_cliente_loja']); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>