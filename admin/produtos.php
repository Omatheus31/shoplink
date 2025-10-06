<?php
require_once '../config/database.php';

try {
    $query = "SELECT id, nome, preco FROM produtos ORDER BY id DESC";
    $stmt = $pdo->query($query);
    $produtos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar produtos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Estilo para a tabela de admin */
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .admin-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <header>
        <h1>Painel de Administração</h1>
        <nav>
            <a href="adicionar_produto.php">Adicionar Novo Produto</a>
        </nav>
    </header>

    <main class="container">
        <h2>Produtos Cadastrados</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    </tr>
            </thead>
            <tbody>
                <?php if ($produtos): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?php echo $produto['id']; ?></td>
                            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                            <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Nenhum produto encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>