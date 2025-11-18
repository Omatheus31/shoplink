<?php
// 1. INCLUI O HEADER DO ADMIN (que já conecta ao $pdo e protege a página)
$titulo_pagina = "Adicionar Produto"; 
require_once 'includes/header_admin.php';

// Busca as categorias do usuário logado para o dropdown
try {
    $query_categorias = "SELECT * FROM categorias WHERE id_usuario = :id_usuario ORDER BY nome ASC";
    $stmt_categorias = $pdo->prepare($query_categorias);
    $stmt_categorias->execute([':id_usuario' => $id_usuario_logado]);
    $categorias = $stmt_categorias->fetchAll();
} catch (PDOException $e) {
    $erro_categorias = "Não foi possível carregar as categorias.";
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Adicionar Novo Produto</h1>
    <a href="produtos.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left-circle-fill"></i> Voltar para Produtos
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <!-- Alerta de sucesso -->
                <?php if (isset($_GET['status']) && $_GET['status'] === 'sucesso'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> Produto salvo com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="salvar_produto.php" method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Produto" required>
                                <label for="nome">Nome do Produto</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0.01" placeholder="Preço (R$)" required>
                                <label for="preco">Preço (R$)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select class="form-select" id="categoria" name="id_categoria">
                            <option value="">Selecione uma categoria (opcional)</option>
                            <?php if (isset($categorias)): ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>">
                                        <?php echo htmlspecialchars($categoria['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <label for="categoria">Categoria</label>
                        <?php if (isset($erro_categorias)): ?>
                            <small class="text-danger"><?php echo $erro_categorias; ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="descricao" name="descricao" placeholder="Descrição" style="height: 100px"></textarea>
                        <label for="descricao">Descrição</label>
                    </div>

                    <div class="mb-3">
                        <label for="imagem" class="form-label">Imagem do Produto (Obrigatório)</label>
                        <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save-fill"></i> Salvar Produto
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>