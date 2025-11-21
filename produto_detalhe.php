<?php
// produto_detalhe.php
require_once 'config/database.php';

// 1. VALIDAÇÃO DO ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_produto = (int)$_GET['id'];

try {
    // 2. BUSCA O PRODUTO (Sem filtro de dono/loja)
    $sql = "SELECT * FROM produtos WHERE id = :id_produto";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_produto' => $id_produto]);
    $produto = $stmt->fetch();
    
    // Se o produto não existir
    if (!$produto) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao buscar o produto: " . $e->getMessage());
}

// 3. INCLUI O HEADER
$titulo_pagina = htmlspecialchars($produto['nome']);
require_once 'includes/header_public.php';
?>

<!-- Link de Voltar -->
<div class="container py-3">
    <a href="index.php" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left"></i> Voltar ao Catálogo
    </a>
</div>

<div class="row g-5 mt-2">
    <!-- Coluna da Imagem -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm bg-white rounded-3 overflow-hidden">
            <div class="product-image-detail d-flex align-items-center justify-content-center bg-light" style="min-height: 400px;">
                <img src="uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" 
                     class="img-fluid" 
                     alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                     style="max-height: 500px; object-fit: contain;">
            </div>
        </div>
    </div>

    <!-- Coluna das Informações -->
    <div class="col-md-6">
        <div class="h-100 d-flex flex-column justify-content-center">
            
            <h1 class="display-5 fw-bold text-dark mb-2"><?php echo htmlspecialchars($produto['nome']); ?></h1>
            
            <div class="mb-4">
                <span class="badge bg-success px-3 py-2 rounded-pill">Disponível</span>
                <!-- Se tivesse categoria, poderia mostrar aqui -->
            </div>

            <p class="display-6 text-success fw-bold mb-4">
                R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
            </p>
            
            <div class="mb-5">
                <h5 class="text-muted mb-3">Sobre o produto:</h5>
                <p class="lead fs-6 text-secondary" style="line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?>
                </p>
            </div>
            
            <!-- Botão de Ação -->
            <div class="d-grid gap-2">
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <button class="btn btn-primary btn-lg py-3 add-to-cart-btn" 
                            data-id="<?php echo $produto['id']; ?>" 
                            data-nome="<?php echo htmlspecialchars($produto['nome']); ?>" 
                            data-preco="<?php echo $produto['preco']; ?>"
                            data-imagem="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
                        <i class="bi bi-cart-plus-fill me-2"></i> Adicionar ao Carrinho
                    </button>
                <?php else: ?>
                    <a href="login.php?redirect_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-dark btn-lg py-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Faça login para comprar
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php require_once 'includes/footer_public.php'; ?>