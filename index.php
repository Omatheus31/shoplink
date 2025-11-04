<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'config/database.php';

try {
    // 1. BUSCAR AS CATEGORIAS PARA OS FILTROS
    $query_categorias = "SELECT * FROM categorias ORDER BY nome ASC";
    $stmt_categorias = $pdo->query($query_categorias);
    $categorias = $stmt_categorias->fetchAll();

    // 2. VERIFICAR SE HÁ UM FILTRO DE CATEGORIA ATIVO NA URL
    $id_categoria_filtro = null;
    if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
        // Verifica se o valor é numérico antes de converter
        if (is_numeric($_GET['categoria'])) {
            $id_categoria_filtro = (int)$_GET['categoria'];
        }
    }

    // 3. MONTAR A QUERY DE PRODUTOS DINAMICAMENTE
    $query_produtos = "SELECT * FROM produtos";
    
    if ($id_categoria_filtro) {
        // Se houver filtro, busca apenas produtos daquela categoria
        $query_produtos .= " WHERE id_categoria = :id_categoria";
    } elseif (isset($_GET['categoria']) && $_GET['categoria'] === 'nenhuma') {
        // Filtro especial para produtos "Sem Categoria"
        $query_produtos .= " WHERE id_categoria IS NULL";
    }
    // Se nenhum filtro, a query busca todos (WHERE não é adicionado)

    $query_produtos .= " ORDER BY id DESC"; // Ordena em qualquer caso
    
    // 4. PREPARAR E EXECUTAR A QUERY DE PRODUTOS
    $stmt_produtos = $pdo->prepare($query_produtos);
    
    if ($id_categoria_filtro) {
        $stmt_produtos->bindParam(':id_categoria', $id_categoria_filtro, PDO::PARAM_INT);
    }
    
    $stmt_produtos->execute();
    $produtos = $stmt_produtos->fetchAll();

} catch (PDOException $e) {
    // Linha 42 CORRIGIDA
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
        /* Estilos para a barra de filtros - CORRIGIDO */
        .category-filters {
            text-align: center;
            margin-bottom: 25px; /* Apenas uma margem inferior para separar do grid */
        }
        .category-filters a {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 20px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }
        .category-filters a:hover, .category-filters a.active {
            background-color: #2c3e50;
            color: white;
            border-color: #2c3e50;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <h1>Catálogo da sua loja</h1>
        <a href="carrinho.php" class="cart-link">
            🛒 Carrinho (<span id="cart-counter">0</span>)
        </a>
    </header>

    <main class="container">

        <nav class="category-filters"> <a href="index.php" 
               class="<?php echo (!$id_categoria_filtro && !isset($_GET['categoria'])) ? 'active' : ''; ?>">
               Todos
            </a>

            <?php foreach ($categorias as $categoria): ?>
                <a href="index.php?categoria=<?php echo $categoria['id']; ?>"
                   class="<?php echo ($id_categoria_filtro == $categoria['id']) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($categoria['nome']); ?>
                </a>
            <?php endforeach; ?>

            <a href="index.php?categoria=nenhuma"
               class="<?php echo (isset($_GET['categoria']) && $_GET['categoria'] === 'nenhuma') ? 'active' : ''; ?>">
               Outros
            </a>
        </nav>

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
                <p style="text-align: center; width: 100%;">Nenhum produto encontrado nesta categoria.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Shoplink - Todos os direitos reservados.</p>
    </footer>

    <script src="assets/js/cart.js"></script>
</body>
</html>