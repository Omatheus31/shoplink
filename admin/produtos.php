<?php
// admin/produtos.php
$titulo_pagina = "Produtos"; 
require_once 'includes/header_admin.php';

// LÓGICA SIMPLIFICADA: Lista todos os produtos + Nome da Categoria
// Usamos LEFT JOIN para trazer o nome da categoria, caso tenha.
$sql = "SELECT p.*, c.nome as nome_categoria 
        FROM produtos p
        LEFT JOIN categorias c ON p.id_categoria = c.id
        ORDER BY p.id DESC";

try {
    $stmt = $pdo->query($sql); // Não precisa mais de prepare/execute com parâmetros
    $produtos = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Produtos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="adicionar_produto.php" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle-fill"></i> Adicionar Novo Produto
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-4">Imagem</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Categoria</th>
                        <th scope="col">Preço</th>
                        <th scope="col" class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($produtos) && $produtos): ?>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="../uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($produto['nome']); ?></td>
                                
                                <td>
                                    <?php echo $produto['nome_categoria'] ? htmlspecialchars($produto['nome_categoria']) : '<span class="text-muted">Sem categoria</span>'; ?>
                                </td>

                                <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                
                                <td class="text-end pe-4">
                                    <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                    <form action="excluir_produto.php" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                        <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash-fill"></i> Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                Nenhum produto encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>