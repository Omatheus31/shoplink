<?php
require_once 'config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_produto = (int)$_GET['id'];

try {
    // Busca produto e nome da categoria
    $sql = "SELECT p.*, c.nome as nome_categoria 
            FROM produtos p 
            LEFT JOIN categorias c ON p.id_categoria = c.id 
            WHERE p.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_produto]);
    $produto = $stmt->fetch();
    
    if (!$produto) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

$titulo_pagina = htmlspecialchars($produto['nome']);
require_once 'includes/header_public.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalhes do Produto</li>
  </ol>
</nav>

<div class="card border-0 shadow-lg overflow-hidden rounded-4">
    <div class="row g-0">
        
        <div class="col-md-6 bg-light d-flex align-items-center justify-content-center p-5">
            <img src="uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" 
                 class="img-fluid rounded shadow-sm" 
                 alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                 style="max-height: 500px; object-fit: contain; transition: transform 0.3s;"
                 onmouseover="this.style.transform='scale(1.05)'"
                 onmouseout="this.style.transform='scale(1)'">
        </div>

        <div class="col-md-6">
            <div class="card-body p-4 p-lg-5 d-flex flex-column h-100">
                
                <div class="mb-2">
                    <span class="badge bg-secondary text-uppercase tracking-wide">
                        <?php echo htmlspecialchars($produto['nome_categoria'] ?? 'Geral'); ?>
                    </span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success ms-2">
                        Disponível
                    </span>
                </div>

                <h1 class="display-5 fw-bold text-dark mb-3"><?php echo htmlspecialchars($produto['nome']); ?></h1>

                <div class="mb-4">
                    <span class="display-6 fw-bold text-primary">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                    <span class="text-muted fs-5 ms-2">à vista</span>
                </div>

                <div class="mb-5">
                    <h5 class="fw-bold h6 text-uppercase text-muted">Sobre o produto</h5>
                    <p class="text-secondary lead fs-6" style="line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?>
                    </p>
                </div>

                <div class="mt-auto">
                    <?php if (isset($_SESSION['id_usuario'])): ?>
                        <button class="btn btn-primary btn-lg w-100 py-3 rounded-3 shadow-sm add-to-cart-btn fw-bold text-uppercase" 
                                style="letter-spacing: 1px;"
                                data-id="<?php echo $produto['id']; ?>" 
                                data-nome="<?php echo htmlspecialchars($produto['nome']); ?>" 
                                data-preco="<?php echo $produto['preco']; ?>"
                                data-imagem="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
                            <i class="bi bi-cart-plus me-2"></i> Adicionar ao Carrinho
                        </button>
                    <?php else: ?>
                        <a href="login.php?redirect_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-dark btn-lg w-100 py-3 rounded-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Entre para Comprar
                        </a>
                    <?php endif; ?>
                    
                    <div class="d-flex align-items-center justify-content-center mt-3 text-muted small">
                        <i class="bi bi-shield-check me-1"></i> Compra 100% Segura e Garantida
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <h4 class="fw-bold mb-4">Quem viu este produto também comprou</h4>
    </div>

<div id="toast-notification">Produto adicionado ao carrinho!</div>

<?php require_once 'includes/footer_public.php'; ?>