<?php
// 1. O Guardião: Nos dá o $id_usuario_logado
require_once 'verifica_login.php'; 
require_once '../config/database.php';

try {
    // --- MUDANÇA AQUI ---
    // Busca apenas os pedidos do usuário logado
    $query = "SELECT id, nome_cliente, total_pedido, data_pedido, status 
              FROM pedidos 
              WHERE id_usuario = :id_usuario 
              ORDER BY id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id_usuario' => $id_usuario_logado]);
    $pedidos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Erro ao buscar pedidos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .admin-table th { background-color: #f2f2f2; }
        .admin-table tr:nth-child(even) { background-color: #f9f9f9; }
        
        .status-pendente {
            background-color: #f39c12;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header class="main-header" style="padding: 15px; margin-bottom: 0;">
        <h1>Painel de Administração</h1>
        <nav>
            <a href="index.php" style="color: white; margin-right: 15px;">Dashboard</a>
            <a href="pedidos.php" style="color: white; margin-right: 15px; font-weight: bold;">Pedidos</a>
            <a href="produtos.php" style="color: white; margin-right: 15px;">Produtos</a>
            <a href="categorias.php" style="color: white; margin-right: 15px;">Categorias</a>
            <a href="adicionar_produto.php" style="color: white; margin-right: 15px;">Adicionar Produto</a>
            <a href="../logout.php" style="color: #ffcccc; margin-right: 15px;">Sair</a>
        </nav>
    </header>

    <main class="container">
        <h2>Pedidos Recebidos</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nº Pedido</th>
                    <th>Cliente</th>
                    <th>Valor Total</th>
                    <th>Data</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pedidos): ?>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?php echo $pedido['id']; ?></td>
                            <td><?php echo htmlspecialchars($pedido['nome_cliente']); ?></td>
                            <td>R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                            
                            <td>
                                <?php if ($pedido['status'] == 'Pendente'): ?>
                                    <span class="status-pendente"><?php echo $pedido['status']; ?></span>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($pedido['status']); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhum pedido encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>