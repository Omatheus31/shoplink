<?php
// 1. O Guardião: Protege a página
require_once 'verifica_login.php'; 
require_once '../config/database.php';

// 2. Verifica se o ID do pedido foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pedidos.php");
    exit();
}
$id_pedido = (int)$_GET['id'];

try {
    // 3. BUSCA OS DADOS DO PEDIDO
    // Garante que o pedido pertence ao utilizador logado
    $sql_pedido = "SELECT * FROM pedidos WHERE id = :id_pedido AND id_usuario = :id_usuario";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([
        ':id_pedido' => $id_pedido,
        ':id_usuario' => $id_usuario_logado
    ]);
    $pedido = $stmt_pedido->fetch();

    // Se o pedido não for encontrado
    if (!$pedido) {
        header("Location: pedidos.php");
        exit();
    }

    // 4. BUSCA OS ITENS DO PEDIDO
    // Usamos um JOIN para pegar o NOME do produto da tabela 'produtos'
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
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido #<?php echo $pedido['id']; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .admin-table th { background-color: #f2f2f2; }
        
        .order-details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr; /* 2/3 para itens, 1/3 para cliente */
            gap: 20px;
        }
        .order-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .order-box h3 { margin-top: 0; }
        .status-form select { padding: 10px; font-size: 1em; }
        .status-form button { padding: 10px 15px; font-size: 1em; background-color: #3498db; color: white; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>
    <header class="main-header">
        <h1>Painel de Administração</h1>
        <nav>
            <a href="index.php" style="color: white; margin-right: 15px;">Dashboard</a>
            <a href="pedidos.php" style="color: white; margin-right: 15px;">Pedidos</a>
            </nav>
    </header>

    <main class="container">
        <a href="pedidos.php" style="text-decoration: none;">&larr; Voltar para Pedidos</a>
        <h2>Detalhes do Pedido #<?php echo $pedido['id']; ?></h2>
        
        <div class="order-details-grid">
            <div class="order-box">
                <h3>Itens do Pedido</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Qtd.</th>
                            <th>Preço Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens_pedido as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nome_produto'] ?? '[Produto Removido]'); ?></td>
                                <td><?php echo $item['quantidade']; ?></td>
                                <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f9f9f9;">
                            <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL DO PEDIDO:</td>
                            <td style="font-weight: bold; font-size: 1.2em;">R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="order-box">
                <h3>Dados do Cliente</h3>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($pedido['nome_cliente']); ?></p>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($pedido['telefone_cliente']); ?></p>
                <p><strong>Endereço:</strong> <?php echo htmlspecialchars($pedido['endereco_cliente']); ?></p>
                <hr>
                
                <h3>Status do Pedido</h3>
                <form class="status-form" action="atualizar_status_pedido.php" method="POST">
                    <input type="hidden" name="id_pedido" value="<?php echo $pedido['id']; ?>">
                    <select name="novo_status">
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
                    <button type="submit">Atualizar Status</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>