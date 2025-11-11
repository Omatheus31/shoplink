<?php
// Inclui a conexão com o banco de dados
require_once 'config/database.php';

// 1. VERIFICAR SE O ID FOI PASSADO PELA URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Se não houver ID, redireciona para o catálogo
    header("Location: index.php");
    exit();
}

$id_produto = (int)$_GET['id'];

try {
    // 2. BUSCAR O PRODUTO NO BANCO DE DADOS
    $sql = "SELECT * FROM produtos WHERE id = :id_produto";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_produto' => $id_produto]);
    
    $produto = $stmt->fetch();

    // Se o produto com esse ID não for encontrado, redireciona
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
        /* Estilos específicos para a página de detalhes */
        .product-detail-container {
            display: grid;
            grid-template-columns: 1fr; /* Uma coluna em telas pequenas */
            gap: 30px;
            margin-top: 30px;
            align-items: start; /* Alinha os itens no topo */
        }
        .product-detail-image img {
            width: 100%; /* Faz a imagem ocupar 100% da sua coluna */
            height: auto;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .product-detail-info h1 {
            margin-top: 0;
            font-size: 2em;
        }
        .product-detail-info .price {
            font-size: 1.8em;
            font-weight: bold;
            color: #27ae60;
            margin: 15px 0;
        }
        .product-detail-info .description {
            font-size: 1.1em;
            line-height: 1.6;
            color: #555;
            margin: 20px 0;
        }
        .product-detail-info .add-to-cart-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .product-detail-info .add-to-cart-btn:hover {
            background-color: #2980b9;
        }

        /* Faz o layout ter duas colunas em telas maiores (desktop) */
        @media (min-width: 768px) {
            .product-detail-container {
                /* Duas colunas: a da imagem (40%) e a de info (60%) */
                grid-template-columns: 2fr 3fr; 
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <h1>Nosso Catálogo</h1>
        <a href="carrinho.php" class="cart-link">
            🛒 Carrinho (<span id="cart-counter">0</span>)
        </a>
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
                
                <button class="add-to-cart-btn" 
                        data-id="<?php echo $produto['id']; ?>" 
                        data-nome="<?php echo htmlspecialchars($produto['nome']); ?>" 
                        data-preco="<?php echo $produto['preco']; ?>"
                        data-imagem="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
                    Adicionar ao Carrinho
                </button>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Shoplink - Todos os direitos reservados.</p>
    </footer>

    <script src="assets/js/cart.js"></script>
</body>
</html>