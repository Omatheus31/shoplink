<?php
$titulo_pagina = "Clientes";
require_once 'includes/header_admin.php';

try {
    // Busca usuários que são clientes (role = 'cliente')
    $sql = "SELECT id, nome, email, data_cadastro 
            FROM usuarios 
            WHERE role = 'cliente' 
            ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = "Erro ao buscar clientes: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Clientes</h1>
</div>

<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
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
                        <th>Data Cadastro</th>
                        </tr>
                </thead>
                <tbody>
                    <?php if ($clientes): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td class="ps-4"><?php echo $cliente['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($cliente['data_cadastro'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                Nenhum cliente cadastrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>