<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Novo Produto - Admin Shoplink</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body>
    <header>
        <h1>Painel de Administração</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="produtos.php">Produtos</a>
            <a href="pedidos.php">Pedidos</a> 
        </nav>
    </header>

    <main>
        <h2>Adicionar Novo Produto</h2>

        <form action="salvar_produto.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="nome">Nome do Produto:</label>
                <input type="text" id="nome" name="nome" required>
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
        <p>&copy; 2025 Shoplink</p>
    </footer>

</body>
</html>