<?php
// 1. O PHP NO TOPO É QUASE O MESMO
require_once 'config/database.php';

// --- ASSUMINDO QUE O SEU ADMIN MASTER (Marcenaria) É ID = 2 ---
// Se for outro ID, mude o '2' abaixo
$id_loja_principal = 2;
// ----------------------------------------------------

// 2. BUSCA DO PRODUTO
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id_produto = (int)$_GET['id'];
try {
    // Busca o produto, garantindo que ele pertença à loja principal
    $sql = "SELECT * FROM produtos WHERE id = :id_produto AND id_usuario = :id_loja";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_produto' => $id_produto,
        ':id_loja' => $id_loja_principal
    ]);
    $produto = $stmt->fetch();
    
    // Se o produto não for encontrado (ou não pertencer à loja principal)
    if (!$produto) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao buscar o produto: " . $e->getMessage());
}

// 3. INCLUI O NOVO CABEÇALHO BOOTSTRAP
// A sessão já é iniciada dentro deste arquivo
$titulo_pagina = htmlspecialchars($produto['nome']); // Define o título da aba
require_once 'includes/header_public.php';
?>

<!-- =============================================== -->
<!-- INÍCIO DO CONTEÚDO DA PÁGINA (Refatorado) -->
<!-- =============================================== -->

<!-- Link para voltar (com classes Bootstrap) -->
<a href="index.php" class="text-decoration-none mb-3 d-inline-block">
    <i class="bi bi-arrow-left"></i> Voltar ao Catálogo
</a>

<!-- Layout de Grid do Bootstrap (row/col) -->
<div class="row g-4">
    <!-- Coluna da Imagem -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <!-- Imagem responsiva do Bootstrap -->
            <img src="uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($produto['nome']); ?>" style="max-height: 500px; object-fit: cover;">
        </div>
    </div>

    <!-- Coluna das Informações -->
    <div class="col-lg-6">
        <div class="d-flex flex-column h-100">
            <!-- Nome do Produto -->
            <h1 class="h2 fw-bold"><?php echo htmlspecialchars($produto['nome']); ?></h1>
            
            <!-- Preço -->
            <p class="text-success fs-2 fw-bold my-3">
                R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
            </p>
            
            <!-- Descrição -->
            <h5 class="mt-3">Descrição</h5>
            <p class="text-muted fs-5" style="line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($produto['descricao'])); // nl2br para quebrar linhas ?>
            </p>
            
            <!-- Botão de Ação (com lógica de login) -->
            <div class="mt-auto"> <!-- Empurra o botão para baixo -->
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <button class="btn btn-primary btn-lg w-100 add-to-cart-btn" 
                            data-id="<?php echo $produto['id']; ?>" 
                            data-nome="<?php echo htmlspecialchars($produto['nome']); ?>" 
                            data-preco="<?php echo $produto['preco']; ?>"
                            data-imagem="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
                        <i class="bi bi-cart-plus-fill"></i> Adicionar ao Carrinho
                    </button>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary btn-lg w-100">
                        Faça login para comprar
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- =============================================== -->
<!-- FIM DO CONTEÚDO DA PÁGINA -->
<!-- =============================================== -->

<?php
// 4. INCLUI O NOVO RODAPÉ BOOTSTRAP
// Ele já inclui o <div id="toast-notification">
require_once 'includes/footer_public.php';
?>