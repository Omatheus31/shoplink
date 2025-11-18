<?php
// 1. INCLUI O HEADER DO ADMIN
$titulo_pagina = "Editar Produto"; 
require_once 'includes/header_admin.php';

// 2. BUSCA DO PRODUTO (com segurança de 3 papéis)
if (isset($_GET['id'])) {
    $id_produto = (int)$_GET['id'];
    
    $sql_produto = "SELECT * FROM produtos WHERE id = :id_produto";
    $params = [':id_produto' => $id_produto];

    // Admin Loja SÓ PODE editar o seu
    if ($_SESSION['role'] === 'admin_loja') {
        $sql_produto .= " AND id_usuario = :id_usuario";
        $params[':id_usuario'] = $id_usuario_logado;
    }
    // Admin Master pode editar qualquer um
    
    try {
        $stmt_produto = $pdo->prepare($sql_produto);
        $stmt_produto->execute($params);
        $produto = $stmt_produto->fetch();

        if (!$produto) {
            header("Location: produtos.php");
            exit();
        }
        
        // Busca as categorias do usuário DONO DO PRODUTO (para o dropdown)
        $stmt_categorias = $pdo->prepare("SELECT * FROM categorias WHERE id_usuario = :id_usuario ORDER BY nome ASC");
        $stmt_categorias->execute([':id_usuario' => $produto['id_usuario']]);
        $categorias = $stmt_categorias->fetchAll();

    } catch (PDOException $e) {
        die("Erro ao buscar dados: " . $e->getMessage());
    }
} else {
    header("Location: produtos.php");
    exit();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Produto</h1>
    <a href="produtos.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left-circle-fill"></i> Voltar para Produtos
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <form action="atualizar_produto.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Produto" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
                                <label for="nome">Nome do Produto</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0.01" placeholder="Preço (R$)" value="<?php echo $produto['preco']; ?>" required>
                                <label for="preco">Preço (R$)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select class="form-select" id="categoria" name="id_categoria">
                            <option value="">Selecione uma categoria (opcional)</option>
                            <?php if (isset($categorias)): ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>" 
                                        <?php echo ($produto['id_categoria'] == $categoria['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($categoria['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <label for="categoria">Categoria</label>
                    </div>

                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="descricao" name="descricao" placeholder="Descrição" style="height: 100px"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
                        <label for="descricao">Descrição</label>
                    </div>

                    <div class="mb-3">
                        <label for="imagem" class="form-label">Alterar Imagem do Produto (opcional)</label>
                        <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*">
                        <div class="mt-2">
                            <small class="text-muted">Imagem atual:</small>
                            <img src="../uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="Imagem atual" class="img-thumbnail" width="100">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save-fill"></i> Atualizar Produto
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>