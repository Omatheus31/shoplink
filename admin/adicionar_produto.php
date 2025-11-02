<?php require_once 'verifica_login.php'; ?>
<?php
// Inclui a conexão com o banco de dados
require_once '../config/database.php';

// Busca todas as categorias para o dropdown
try {
    $query_categorias = "SELECT * FROM categorias ORDER BY nome ASC";
    $stmt_categorias = $pdo->query($query_categorias);
    $categorias = $stmt_categorias->fetchAll();
} catch (PDOException $e) {
    // Se der erro, continua sem as categorias, mas registra o erro
    $erro_categorias = "Não foi possível carregar as categorias.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Novo Produto - Admin Shoplink</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Adicionando estilo para o select */
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-sucesso {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>
    <header class="main-header" style="padding: 15px; margin-bottom: 0;">
        <h1>Painel de Administração</h1>
        <nav>
            <a href="index.php" style="color: white; margin-right: 15px;">Dashboard</a>
            <a href="pedidos.php" style="color: white; margin-right: 15px;">Pedidos</a>
            <a href="produtos.php" style="color: white; margin-right: 15px;">Produtos</a>
            <a href="categorias.php" style="color: white; margin-right: 15px;">Categorias</a>
            <a href="adicionar_produto.php" style="color: white;  font-weight: bold;">Adicionar Produto</a>
            <a href="../logout.php" style="color: #ffcccc; margin-left: auto;">Sair</a>
        </nav>
    </header>

    <main class="container">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'sucesso'): ?>
            <div class="alert alert-sucesso">
                Produto salvo com sucesso!
            </div>
        <?php endif; ?>
        <h2>Adicionar Novo Produto</h2>

        <form action="salvar_produto.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="nome">Nome do Produto:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div>
                <label for="categoria">Categoria:</label>
                <select id="categoria" name="id_categoria">
                    <option value="">Selecione uma categoria (opcional)</option>
                    <?php if (isset($categorias)): ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>">
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (isset($erro_categorias)): ?>
                    <p style="color: red;"><?php echo $erro_categorias; ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="4"></textarea>
            </div>

            <div>
                <label for="preco">Preço (R$):</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0.01" required>
            </div>

            <div>
                <label for="imagem">Imagem do Produto:</label>
                <input type="file" id="imagem" name="imagem" accept="image/*" required>
            </div>

            <div>
                <button type="submit">Salvar Produto</button>
            </div>
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Shoplink</p>
    </footer>

</body>
</html>