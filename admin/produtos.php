<?php
// 1. INCLUI O HEADER DO ADMIN (que já conecta ao $pdo e protege a página)
$titulo_pagina = "Produtos"; // Define o título da aba
require_once 'includes/header_admin.php';

// 2. LÓGICA DE 3 PAPÉIS
$sql = "";
$params = [];

if ($_SESSION['role'] === 'admin_master') {
    // Admin Master vê TUDO, com o nome da loja
    $sql = "SELECT p.*, u.nome_loja as nome_da_loja 
            FROM produtos p
            JOIN usuarios u ON p.id_usuario = u.id
            ORDER BY p.id DESC";
} else {
    // Admin Loja vê SÓ O DELE
    $sql = "SELECT * FROM produtos 
            WHERE id_usuario = :id_usuario 
            ORDER BY id DESC";
    $params[':id_usuario'] = $id_usuario_logado;
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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
                        <th scope="col">Preço</th>
                        <?php if ($_SESSION['role'] === 'admin_master'): ?>
                            <th scope="col">Loja</th> <!-- Só o Admin Master vê esta coluna -->
                        <?php endif; ?>
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
                                <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                
                                <?php if ($_SESSION['role'] === 'admin_master'): ?>
                                    <td><?php echo htmlspecialchars($produto['nome_da_loja']); ?></td>
                                <?php endif; ?>
                                
                                <td class="text-end pe-4">
                                    <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                    <!-- Formulário de Exclusão -->
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
                            <td colspan="<?php echo ($_SESSION['role'] === 'admin_master') ? '6' : '5'; ?>" class="text-center py-5 text-muted">
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