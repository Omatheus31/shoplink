<?php
// 1. O PHP NO TOPO É QUASE O MESMO
require_once 'config/database.php';

try {
    // 2. BUSCAR AS CATEGORIAS
    $query_categorias = "SELECT * FROM categorias WHERE id_usuario = 2";
    $stmt_categorias = $pdo->query($query_categorias);
    $categorias = $stmt_categorias->fetchAll();

    // 3. LÓGICA DE FILTRO DE PRODUTOS
    $id_categoria_filtro = null;
    if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
        if (is_numeric($_GET['categoria'])) {
            $id_categoria_filtro = (int)$_GET['categoria'];
        }
    }

    // 4. QUERY DE PRODUTOS
    $params = [];
    $query_produtos = "SELECT * FROM produtos WHERE id_usuario = :id_usuario_loja"; // Loja Principal ID 1
    $params[':id_usuario_loja'] = 2;

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

// 5. INCLUI O NOVO CABEÇALHO BOOTSTRAP
// A sessão já é iniciada e o <main> é aberto dentro deste arquivo
require_once 'includes/header_public.php';

$titulo_pagina = "Catálogo Online"; 
?>

<nav class="nav nav-pills flex-column flex-sm-row justify-content-center mb-4">
    <a class="flex-sm-fill text-sm-center nav-link <?php echo (!$id_categoria_filtro && !isset($_GET['categoria'])) ? 'active' : 'text-dark'; ?>" 
       href="index.php">
       Todos
    </a>

    <?php foreach ($categorias as $categoria): ?>
        <a class="flex-sm-fill text-sm-center nav-link <?php echo ($id_categoria_filtro == $categoria['id']) ? 'active' : 'text-dark'; ?>"
           href="index.php?categoria=<?php echo $categoria['id']; ?>">
            <?php echo htmlspecialchars($categoria['nome']); ?>
        </a>
    <?php endforeach; ?>

    <a class="flex-sm-fill text-sm-center nav-link <?php echo (isset($_GET['categoria']) && $_GET['categoria'] === 'nenhuma') ? 'active' : 'text-dark'; ?>"
       href="index.php?categoria=nenhuma">
       Outros
    </a>
</nav>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php if ($produtos): ?>
        <?php foreach ($produtos as $produto): ?>
            
            <div class="col">
                <div class="card h-100 shadow-sm product-card">
                    <img src="uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <a href="produto_detalhe.php?id=<?php echo $produto['id']; ?>" class="text-dark text-decoration-none">
                                <?php echo htmlspecialchars($produto['nome']); ?>
                            </a>
                        </h5>
                        <p class="card-text text-success fs-4 fw-bold">
                            R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                        </p>
                        <p class="card-text text-muted small flex-grow-1">
                            <?php echo htmlspecialchars($produto['descricao']); ?>
                        </p>
                        
                        <?php if (isset($_SESSION['id_usuario'])): ?>
                            <button class="btn btn-primary add-to-cart-btn" 
                                    data-id="<?php echo $produto['id']; ?>" 
                                    data-nome="<?php echo htmlspecialchars($produto['nome']); ?>" 
                                    data-preco="<?php echo $produto['preco']; ?>"
                                    data-imagem="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
                                <i class="bi bi-cart-plus-fill"></i> Adicionar ao Carrinho
                            </button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-secondary">
                                Faça login para comprar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div> <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <p class="alert alert-warning text-center">Nenhum produto encontrado nesta categoria.</p>
        </div>
    <?php endif; ?>
</div> <div id="toast-notification">Produto adicionado ao carrinho!</div>

<?php
// 6. INCLUI O NOVO RODAPÉ BOOTSTRAP
// Ele fecha o <main>, <body> e <html> e inclui os JS
require_once 'includes/footer_public.php';
?>