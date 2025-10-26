<?php
require_once '../config/database.php';

try {
    // Agora selecionamos todos os campos para a edição
    $query = "SELECT id, nome, preco, descricao, imagem_url FROM produtos ORDER BY id DESC";
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
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .admin-table th { background-color: #f2f2f2; }
        .admin-table tr:nth-child(even) { background-color: #f9f9f9; }
        
        /* Estilos para os botões de ação */
        .action-btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            color: white;
            margin-right: 5px;
            font-size: 0.9em;
        }
        .edit-btn { background-color: #3498db; }
        .edit-btn:hover { background-color: #2980b9; }
        .delete-btn { background-color: #e74c3c; border: none; cursor: pointer; font-family: inherit; }
        .delete-btn:hover { background-color: #c0392b; }

    </style>
</head>
<body>
    <header class="main-header" style="padding: 15px; margin-bottom: 0;"> <h1>Painel de Administração</h1>
        <nav>
            <a href="pedidos.php" style="color: white; margin-right: 15px;">Pedidos</a> 
            <a href="adicionar_produto.php" style="color: white;">Adicionar Novo Produto</a>
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
                    <th>Ações</th> </tr>
            </thead>
            <tbody>
                <?php if ($produtos): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?php echo $produto['id']; ?></td>
                            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                            <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                            
                            <td>
                                <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="action-btn edit-btn">Editar</a>
                                
                                <form action="excluir_produto.php" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                    <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                                    <button type="submit" class="action-btn delete-btn">Excluir</button>
                                </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nenhum produto encontrado.</td> </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>