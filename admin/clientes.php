<?php
$titulo_pagina = "Clientes";
require_once 'includes/header_admin.php';

try {
    $sql = "SELECT * FROM usuarios WHERE role = 'cliente' ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = "Erro: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestão de Clientes</h1>
    </div>

<?php if (isset($_GET['msg'])): ?>
    <?php 
        $msg = $_GET['msg'];
        $txt = ""; $cor = "success";
        if ($msg == 'atualizado') $txt = "Dados do cliente atualizados!";
        if ($msg == 'deletado') { $txt = "Cliente removido com sucesso."; $cor = "danger"; }
    ?>
    <?php if ($txt): ?>
        <div class="alert alert-<?php echo $cor; ?> alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> <?php echo $txt; ?>
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
                        <th class="ps-4">ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($clientes): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td class="ps-4">#<?php echo $cliente['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                                <td class="text-end pe-4">
                                    <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger btn-excluir-cliente" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalExcluirCliente"
                                            data-id="<?php echo $cliente['id']; ?>"
                                            data-nome="<?php echo htmlspecialchars($cliente['nome']); ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Nenhum cliente cadastrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluirCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-danger">Excluir Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <i class="bi bi-person-x text-warning display-1 mb-3"></i>
        <p class="mb-1">Tem certeza que deseja excluir:</p>
        <h4 class="fw-bold" id="nomeClienteExcluir">...</h4>
        <p class="text-danger small fw-bold">Atenção: Todos os pedidos deste cliente também serão apagados!</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="linkExcluirFinal" class="btn btn-danger px-4">Sim, Excluir</a>
      </div>
    </div>
  </div>
</div>

<script>
    const modalExcluir = document.getElementById('modalExcluirCliente');
    modalExcluir.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nome = button.getAttribute('data-nome');
        
        document.getElementById('nomeClienteExcluir').textContent = nome;
        document.getElementById('linkExcluirFinal').href = 'excluir_cliente.php?id=' + id;
    });
</script>

<?php require_once 'includes/footer_admin.php'; ?>