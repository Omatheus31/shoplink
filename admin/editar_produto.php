<?php
require_once '../config/database.php';

// 1. VERIFICAR SE O ID FOI PASSADO PELA URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 2. BUSCAR O PRODUTO ESPECÍFICO
        $sql_produto = "SELECT * FROM produtos WHERE id = :id";
        $stmt_produto = $pdo->prepare($sql_produto);
        $stmt_produto->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_produto->execute();
        $produto = $stmt_produto->fetch();

        if (!$produto) {
            header("Location: produtos.php");
            exit();
        }

        // 3. BUSCAR TODAS AS CATEGORIAS PARA O DROPDOWN
        $query_categorias = "SELECT * FROM categorias ORDER BY nome ASC";
        $stmt_categorias = $pdo->query($query_categorias);
        $categorias = $stmt_categorias->fetchAll();

    } catch (PDOException $e) {
        die("Erro ao buscar dados: " . $e->getMessage());
    }
} else {
    header("Location: produtos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - Admin Shoplink</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header class="main-header" style="padding: 15px; margin-bottom: 0;">
        <h1>Painel de Administração</h1>
        <nav>
            <a href="pedidos.php" style="color: white; margin-right: 15px;">Pedidos</a>
            <a href="produtos.php" style="color: white; margin-right: 15px;">Produtos</a>
            <a href="categorias.php" style="color: white; margin-right: 15px;">Categorias</a>
        </nav>
    </header>

    <main class="container">
        <h2>Editar Produto: <?php echo htmlspecialchars($produto['nome']); ?></h2>

        <form action="atualizar_produto.php" method="post" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">

            <div>
                <label for="nome">Nome do Produto:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
            </div>

            <div>
                <label for="categoria">Categoria:</label>
                <select id="categoria" name="id_categoria">
                    <option value="">Selecione uma categoria (opcional)</option>
                    <?php if (isset($categorias)): ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>" 
                                <?php echo ($produto['id_categoria'] == $categoria['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
            </div>

            <div>
                <label for="preco">Preço (R$):</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0.01" value="<?php echo $produto['preco']; ?>" required>
            </div>

            <div>
                <label for="imagem">Alterar Imagem do Produto (opcional):</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">
                <p>Imagem atual:</p>
                <img src="../uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="Imagem atual" width="100">
            </div>

            <div>
                <button type="submit">Atualizar Produto</button>
            </div>
        </form>
    </main>
</body>
</html>