<?php
// Inclui a conexão com o banco de dados
require_once '../config/database.php';

// 1. VERIFICAR SE O ID FOI PASSADO PELA URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 2. BUSCAR O PRODUTO NO BANCO DE DADOS
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // fetch() busca apenas um registro
        $produto = $stmt->fetch();

        // Se não encontrar o produto, redireciona para a lista
        if (!$produto) {
            header("Location: produtos.php");
            exit();
        }

    } catch (PDOException $e) {
        die("Erro ao buscar o produto: " . $e->getMessage());
    }
} else {
    // Se nenhum ID for passado, redireciona para a lista
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
</head>
<body>
    <header class="main-header" style="padding: 15px; margin-bottom: 0;">
        <h1>Painel de Administração</h1>
        <nav>
            <a href="produtos.php" style="color: white;">Voltar para Produtos</a>
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