<?php
// 1. INICIA A SESSÃO para verificar se o utilizador está logado
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once 'config/database.php';

try {
    // 2. BUSCAR AS CATEGORIAS PARA OS FILTROS
    $query_categorias = "SELECT * FROM categorias WHERE id_usuario = 1"; // Mostra só as categorias do Admin Master (Loja Principal)
    $stmt_categorias = $pdo->query($query_categorias);
    $categorias = $stmt_categorias->fetchAll();

    // 3. LÓGICA DE FILTRO DE PRODUTOS (não muda)
    $id_categoria_filtro = null;
    if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
        if (is_numeric($_GET['categoria'])) {
            $id_categoria_filtro = (int)$_GET['categoria'];
        }
    }

    // 4. MONTAR A QUERY DE PRODUTOS DINAMICAMENTE
    $query_produtos = "SELECT * FROM produtos";
    $params = []; // Array para os parâmetros

    // O catálogo público mostra os produtos do admin (id_usuario = 1)
    $query_produtos .= " WHERE id_usuario = 1"; // Mostra apenas produtos da loja principal
    $params[':id_usuario_loja'] = 1;

    if ($id_categoria_filtro) {
        $query_produtos .= " AND id_categoria = :id_categoria";
        $params[':id_categoria'] = $id_categoria_filtro;
    } elseif (isset($_GET['categoria']) && $_GET['categoria'] === 'nenhuma') {
        $query_produtos .= " AND id_categoria IS NULL";
    }
    
    $query_produtos .= " ORDER BY id DESC";
    
    $stmt_produtos = $pdo->prepare($query_produtos);
    // Remove :id_usuario_loja se não estiver na query principal (bug fix)
    if (!strpos($query_produtos, ":id_usuario_loja")) {
       unset($params[':id_usuario_loja']);
    }
    // Adiciona o :id_usuario_loja apenas se o where principal existir
    if (strpos($query_produtos, "WHERE id_usuario = 1")) {
         // Não precisa de bind, já está na string. Vamos refatorar:
    }

    // --- REFAZENDO A LÓGICA DA QUERY DE FORMA MAIS LIMPA ---
    $params = [];
    $query_produtos = "SELECT * FROM produtos WHERE id_usuario = :id_usuario_loja"; // Loja Principal ID 1
    $params[':id_usuario_loja'] = 1;

    if ($id_categoria_filtro) {
        $query_produtos .= " AND id_categoria = :id_categoria";
        $params[':id_categoria'] = $id_categoria_filtro;
    } elseif (isset($_GET['categoria']) && $_GET['categoria'] === 'nenhuma') {
        $query_produtos .= " AND id_categoria IS NULL";
    }
    
    $query_produtos .= " ORDER BY id DESC";
    
    $stmt_produtos = $pdo->prepare($query_produtos);
    $stmt_produtos->execute($params);
    $produtos = $stmt_produtos->fetchAll();


} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Online - Shoplink</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .category-filters { text-align: center; margin-bottom: 25px; }
        .category-filters a { display: inline-block; padding: 8px 15px; margin: 5px; background-color: #fff; border: 1px solid #ddd; border-radius: 20px; text-decoration: none; color: #333; transition: background-color 0.3s, color 0.3s; }
        .category-filters a:hover, .category-filters a.active { background-color: #2c3e50; color: white; border-color: #2c3e50; }
        
        /* ESTILO PARA O NOVO BOTÃO DE LOGIN */
        .login-to-buy-btn {
            display: block;
            background-color: #7f8c8d; /* Cinza */
            color: white !important;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 1em;
            transition: background-color 0.3s;
            margin-top: 10px; /* Para alinhar com o botão "Adicionar" */
        }
        .login-to-buy-btn:hover { background-color: #6c7a7b; }
    </style>
</head>
<body>
    <header class="main-header">
        <h1>Catálogo da sua loja</h1>
        
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
        <nav class="category-filters">
            <a href="index.php" class="<?php echo (!$id_categoria_filtro && !isset($_GET['categoria'])) ? 'active' : ''; ?>">Todos</a>
            <?php foreach ($categorias as $categoria): ?>
                <a href="index.php?categoria=<?php echo $categoria['id']; ?>" class="<?php echo ($id_categoria_filtro == $categoria['id']) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($categoria['nome']); ?>
                </a>
            <?php endforeach; ?>
            <a href="index.php?categoria=nenhuma" class="<?php echo (isset($_GET['categoria']) && $_GET['categoria'] === 'nenhuma') ? 'active' : ''; ?>">Outros</a>
        </nav>

        <div class="product-grid">
            <?php if ($produtos): ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="product-card">
                        <img src="uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                        <h3>
                            <a href="produto_detalhe.php?id=<?php echo $produto['id']; ?>">
                                <?php echo htmlspecialchars($produto['nome']); ?>
                            </a>
                        </h3>
                        <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <p class="description"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                        
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
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; width: 100%;">Nenhum produto encontrado nesta categoria.</p>
            <?php endif; ?>
        </div>
    </main>

    <div id="toast-notification">Produto adicionado ao carrinho!</div>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Shoplink</p>
    </footer>

    <script src="assets/js/cart.js"></script>
</body>
</html>