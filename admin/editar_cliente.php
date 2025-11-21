<?php
require_once 'includes/header_admin.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: clientes.php');
    exit();
}

try {
    $stmt = $pdo->prepare('SELECT id, nome_loja, email, COALESCE(role, "cliente") AS role FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $cliente = $stmt->fetch();
    if (!$cliente) {
        header('Location: clientes.php');
        exit();
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
}
?>

<h1 class="h3 mb-3">Editar Cliente</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="salvar_cliente.php" method="post">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome_loja" class="form-control" value="<?php echo htmlspecialchars($cliente['nome_loja']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nova Senha (deixe em branco para não alterar)</label>
                <input type="password" name="senha" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="cliente" <?php echo ($cliente['role'] == 'cliente') ? 'selected' : ''; ?>>Cliente</option>
                    <option value="admin_loja" <?php echo ($cliente['role'] == 'admin_loja') ? 'selected' : ''; ?>>Admin Loja</option>
                    <option value="admin_master" <?php echo ($cliente['role'] == 'admin_master') ? 'selected' : ''; ?>>Admin Master</option>
                </select>
            </div>
            <button class="btn btn-primary">Salvar Alterações</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer_admin.php';
