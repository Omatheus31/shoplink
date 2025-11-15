<?php
// 1. INICIA A SESSÃO
session_start();

// Inclui a conexão com o banco de dados
require_once 'config/database.php';

// ... (Lógica de buscar o produto não muda) ...
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id_produto = (int)$_GET['id'];
try {
    $sql = "SELECT * FROM produtos WHERE id = :id_produto";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_produto' => $id_produto]);
    $produto = $stmt->fetch();
    if (!$produto) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao buscar o produto: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produto['nome']); ?> - Shoplink</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .product-detail-container { display: grid; grid-template-columns: 1fr; gap: 30px; margin-top: 30px; align-items: start; }
        .product-detail-image img { width: 100%; height: auto; border-radius: 8px; border: 1px solid #ddd; }
        .product-detail-info h1 { margin-top: 0; font-size: 2em; }
        .product-detail-info .price { font-size: 1.8em; font-weight: bold; color: #27ae60; margin: 15px 0; }
        .product-detail-info .description { font-size: 1.1em; line-height: 1.6; color: #555; margin: 20px 0; }
        .product-detail-info .add-to-cart-btn { background-color: #3498db; color: white; border: none; padding: 15px 25px; border-radius: 5px; cursor: pointer; font-size: 1.1em; font-weight: bold; transition: background-color 0.3s; }
        .product-detail-info .add-to-cart-btn:hover { background-color: #2980b9; }

        /* NOVO ESTILO (copiado do index.php) */
        .login-to-buy-btn {
            display: inline-block;
            background-color: #7f8c8d;
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .login-to-buy-btn:hover { background-color: #6c7a7b; }

        @media (min-width: 768px) {
            .product-detail-container { grid-template-columns: 2fr 3fr; }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <h1>Nosso Catálogo</h1>
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="carrinho.php" class="cart-link">
                🛒 Carrinho (<span id="cart-counter">0</span>)
            </a>
            
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <a href="minha_conta.php" class="cart-link" style="background-color: #3498db;">Minha Conta</a>
                <a href="logout.php" class="cart-link" style="background-color: #e74c3c;">Sair</a>
            <?php else: ?>
                <a href="login.php" class="cart-link" style="background-color: #3498db;">Login / Cadastrar</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="container">
        <a href="index.php" style="text-decoration: none;">&larr; Voltar ao Catálogo</a>
        <div class="product-detail-container">
            <div class="product-detail-image">
                <img src="uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
            </div>
            <div class="product-detail-info">
                <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
                <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                <p class="description">
                    <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?>
                </p>
                
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <button class="add-to-cart-btn" 
                            data-id="<?php echo $produto['id']; ?>" 
                            data-nome="<?php echo htmlspecialchars($produto['nome']); ?>" 
                            data-preco="<?php echo $produto['preco']; ?>"
                            data-imagem="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
                        Adicionar ao Carrinho
                    </button>
                <?php else: ?>
                    <a href="login.php" class="login-to-buy-btn">
                        Faça login para comprar
                    </a>
                <?php endif; ?>
                </div>
        </div>
    </main>

    <div id="toast-notification">Produto adicionado ao carrinho!</div>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Shoplink</p>
    </footer>
    <script src="assets/js/cart.js"></script>
</body>
</html>