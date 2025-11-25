<?php
$titulo_pagina = "Produtos"; 
require_once 'includes/header_admin.php';

// Busca Produtos
try {
    $sql = "SELECT p.*, c.nome as nome_categoria 
            FROM produtos p
            LEFT JOIN categorias c ON p.id_categoria = c.id
            ORDER BY p.id DESC";
    $stmt = $pdo->query($sql);
    $produtos = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Produtos</h1>
    <a href="adicionar_produto.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Novo Produto
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php 
        $msg = $_GET['msg'];
        $texto = "";
        $cor = "success";
        
        if ($msg == 'criado') $texto = "Produto criado com sucesso!";
        if ($msg == 'atualizado') $texto = "Produto atualizado com sucesso!";
        if ($msg == 'deletado') { $texto = "Produto excluído."; $cor = "danger"; }
    ?>
    <?php if ($texto): ?>
        <div class="alert alert-<?php echo $cor; ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> <?php echo $texto; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Imagem</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($produtos): ?>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="../uploads/<?php echo htmlspecialchars($produto['imagem_url']); ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;" class="border">
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($produto['nome']); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?php echo htmlspecialchars($produto['nome_categoria'] ?? 'Sem categoria'); ?>
                                    </span>
                                </td>
                                <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                <td class="text-end pe-4">
                                    <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-excluir" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalExcluir"
                                            data-id="<?php echo $produto['id']; ?>"
                                            data-nome="<?php echo htmlspecialchars($produto['nome']); ?>">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Nenhum produto cadastrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-danger">Excluir Produto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <i class="bi bi-exclamation-circle text-warning display-1 mb-3"></i>
        <p class="mb-1">Tem certeza que deseja excluir o produto:</p>
        <h4 class="fw-bold" id="nomeProdutoExcluir">...</h4>
        <p class="text-muted small">Essa ação não pode ser desfeita.</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
        <form action="excluir_produto.php" method="POST" class="d-inline">
            <input type="hidden" name="id" id="idProdutoExcluir">
            <button type="submit" class="btn btn-danger px-4">Sim, Excluir</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    const modalExcluir = document.getElementById('modalExcluir');
    modalExcluir.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nome = button.getAttribute('data-nome');
        
        document.getElementById('idProdutoExcluir').value = id;
        document.getElementById('nomeProdutoExcluir').textContent = nome;
    });
</script>

<?php require_once 'includes/footer_admin.php'; ?>