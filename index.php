<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'config/database.php';

try {
    // Prepara e executa a query para selecionar todos os produtos
    // ORDER BY id DESC faz com que os produtos mais recentes apareçam primeiro
    $query = "SELECT * FROM produtos ORDER BY id DESC";
    $stmt = $pdo->query($query);
    
    // Busca todos os produtos como um array associativo
    $produtos = $stmt->fetchAll();

} catch (PDOException $e) {
    // Em caso de erro, exibe uma mensagem
    die("Erro ao buscar produtos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Online - Shoplink</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="main-header">
        <h1>Nosso Catálogo</h1>
        </header>

    <main class="container">
        <div class="product-grid">
            <?php if ($produtos): ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="product-card">
                        <img src="uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                        <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                        <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <p class="description"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                        <button class="add-to-cart-btn" 
                                data-id="<?php echo $produto['id']; ?>" 
                                data-nome="<?php echo htmlspecialchars($produto['nome']); ?>" 
                                data-preco="<?php echo $produto['preco']; ?>"
                                data-imagem="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
                            Adicionar ao Carrinho
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum produto cadastrado no momento.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Shoplink - Todos os direitos reservados.</p>
    </footer>
    <script src="assets/js/cart.js"></script>
</body>
</html>