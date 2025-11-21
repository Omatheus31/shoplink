<?php
// index.php
require_once 'config/database.php';

try {
    // 1. BUSCAR TODAS AS CATEGORIAS (Sem filtro de usuário)
    $query_categorias = "SELECT * FROM categorias ORDER BY nome ASC";
    $stmt_categorias = $pdo->query($query_categorias);
    $categorias = $stmt_categorias->fetchAll();

    // 2. LÓGICA DE FILTROS
    $id_categoria_filtro = null;
    $q = null;
    
    if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
        // Verifica se é número (ID) ou string 'nenhuma'
        if (is_numeric($_GET['categoria'])) {
            $id_categoria_filtro = (int)$_GET['categoria'];
        }
    }
    
    if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
        $q = trim($_GET['q']);
    }

    // 3. QUERY DE PRODUTOS (Refatorada para Loja Única)
    // Começamos com 1=1 para facilitar a concatenação dos ANDs
    $query_produtos = "SELECT * FROM produtos WHERE 1=1";
    $params = [];

    // Filtro por Categoria
    if ($id_categoria_filtro) {
        $query_produtos .= " AND id_categoria = :id_categoria";
        $params[':id_categoria'] = $id_categoria_filtro;
    } elseif (isset($_GET['categoria']) && $_GET['categoria'] === 'nenhuma') {
        // Produtos sem categoria definida
        $query_produtos .= " AND id_categoria IS NULL";
    }

    // Filtro de Busca (Nome ou Descrição)
    if ($q) {
        $query_produtos .= " AND (nome LIKE :q OR descricao LIKE :q)";
        $params[':q'] = '%' . $q . '%';
    }
    
    $query_produtos .= " ORDER BY id DESC";
    
    $stmt_produtos = $pdo->prepare($query_produtos);
    $stmt_produtos->execute($params);
    $produtos = $stmt_produtos->fetchAll();

} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}

// 4. INCLUI O CABEÇALHO
// A sessão é iniciada dentro do header_public.php
require_once 'includes/header_public.php';
?>

<div class="row">
    <!-- Barra Lateral de Filtros -->
    <div class="col-lg-3 mb-4">
        <div class="card shadow-sm filter-card">
            <div class="card-body">
                <h5 class="card-title">Filtrar</h5>
                <form method="get" action="index.php">
                    <div class="mb-3">
                        <label class="form-label visually-hidden">Pesquisar</label>
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($q ?? ''); ?>">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="small text-muted">Categorias</h6>
                        <div class="list-group list-group-flush">
                            <a href="index.php" class="list-group-item list-group-item-action <?php echo (!$id_categoria_filtro && !isset($_GET['categoria'])) ? 'active' : ''; ?>">Todas</a>
                            <?php foreach ($categorias as $categoria): ?>
                                <a href="index.php?categoria=<?php echo $categoria['id']; ?>" class="list-group-item list-group-item-action <?php echo ($id_categoria_filtro == $categoria['id']) ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($categoria['nome']); ?>
                                </a>
                            <?php endforeach; ?>
                            <a href="index.php?categoria=nenhuma" class="list-group-item list-group-item-action <?php echo (isset($_GET['categoria']) && $_GET['categoria'] === 'nenhuma') ? 'active' : ''; ?>">Sem Categoria</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="col-lg-9">
        <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3">
            <?php if ($produtos): ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="col">
                        <div class="card h-100 product-card shadow-sm border-0">
                            <div class="product-image-wrap position-relative">
                                <a href="produto_detalhe.php?id=<?php echo $produto['id']; ?>">
                                    <img src="uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-2">
                                    <a href="produto_detalhe.php?id=<?php echo $produto['id']; ?>" class="text-dark text-decoration-none fw-bold">
                                        <?php echo htmlspecialchars($produto['nome']); ?>
                                    </a>
                                </h6>
                                <p class="card-text text-muted small flex-grow-1 mb-3 text-truncate">
                                    <?php echo htmlspecialchars($produto['descricao']); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <div class="price">
                                        <div class="fw-bold text-success fs-5">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
                                    </div>
                                    
                                    <!-- Botão de Compra -->
                                    <?php if (isset($_SESSION['id_usuario'])): ?>
                                        <button class="btn btn-success btn-sm add-to-cart-btn" 
                                                data-id="<?php echo $produto['id']; ?>" 
                                                data-nome="<?php echo htmlspecialchars($produto['nome']); ?>" 
                                                data-preco="<?php echo $produto['preco']; ?>"
                                                data-imagem="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
                                            <i class="bi bi-cart-plus-fill"></i> Comprar
                                        </button>
                                    <?php else: ?>
                                        <a href="login.php?redirect_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-outline-secondary btn-sm">
                                            Login
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center py-5">
                        <i class="bi bi-search fs-1 d-block mb-3"></i>
                        Nenhum produto encontrado com os filtros atuais.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Notificação Toast (Usada pelo JS do carrinho) -->
<div id="toast-notification">Produto adicionado ao carrinho!</div>

<?php require_once 'includes/footer_public.php'; ?>