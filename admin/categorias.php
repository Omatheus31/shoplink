<?php
$titulo_pagina = "Categorias";
require_once 'includes/header_admin.php';

// --- LÓGICA DE ADICIONAR (EMBUTIDA PARA FACILITAR) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_categoria'])) {
    $nome = trim($_POST['nome_categoria']);
    if (!empty($nome)) {
        try {
            $sql = "INSERT INTO categorias (nome) VALUES (:nome)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nome' => $nome]);
            echo "<div class='alert alert-success m-3'>Categoria adicionada!</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger m-3'>Erro: " . $e->getMessage() . "</div>";
        }
    }
}

// --- LÓGICA DE EXCLUIR ---
if (isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    try {
        $pdo->query("DELETE FROM categorias WHERE id = $id");
        echo "<script>window.location.href='categorias.php';</script>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger m-3'>Erro ao excluir (pode haver produtos vinculados).</div>";
    }
}

// --- LISTAGEM ---
try {
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY id DESC");
    $categorias = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gerenciar Categorias</h1>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Adicionar Nova</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nome da Categoria</label>
                        <input type="text" name="nome_categoria" class="form-control" required placeholder="Ex: Móveis">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Salvar Categoria</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Categorias Existentes</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nome</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($categorias): ?>
                            <?php foreach ($categorias as $cat): ?>
                                <tr>
                                    <td class="ps-4"><?php echo $cat['id']; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($cat['nome']); ?></td>
                                    <td class="text-end pe-4">
                                        <a href="categorias.php?excluir=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted">Nenhuma categoria encontrada.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>