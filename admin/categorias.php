<?php
// 1. INCLUI O HEADER DO ADMIN (que já conecta ao $pdo e protege a página)
$titulo_pagina = "Categorias";
require_once 'includes/header_admin.php';

// PARTE 1: Processar o formulário de NOVA CATEGORIA
$erro = '';
$sucesso = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_categoria'])) {
    $nome_categoria = trim($_POST['nome_categoria']);
    
    if (!empty($nome_categoria)) {
        try {
            $sql_insert = "INSERT INTO categorias (nome, id_usuario) VALUES (:nome, :id_usuario)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([
                ':nome' => $nome_categoria,
                ':id_usuario' => $id_usuario_logado
            ]);
            $sucesso = "Categoria \"$nome_categoria\" criada com sucesso!";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                // Erro de 'nome_por_usuario' (duplicado)
                $erro = "Erro: Você já possui uma categoria com este nome.";
            } else {
                $erro = "Erro ao salvar categoria: " . $e->getMessage();
            }
        }
    } else {
        $erro = "O nome da categoria não pode estar vazio.";
    }
}

// PARTE 2: Buscar categorias (com lógica de 3 papéis)
$sql_categorias = "";
$params_cat = [];

if ($_SESSION['role'] === 'admin_master') {
    // Admin Master vê TUDO
    $sql_categorias = "SELECT c.*, u.nome_loja FROM categorias c JOIN usuarios u ON c.id_usuario = u.id ORDER BY u.nome_loja, c.nome ASC";
} else {
    // Admin Loja vê SÓ O DELE
    $sql_categorias = "SELECT * FROM categorias WHERE id_usuario = :id_usuario ORDER BY nome ASC";
    $params_cat[':id_usuario'] = $id_usuario_logado;
}

try {
    $stmt_cat = $pdo->prepare($sql_categorias);
    $stmt_cat->execute($params_cat);
    $categorias = $stmt_cat->fetchAll();
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Erro ao buscar categorias: ' . $e->getMessage() . '</div>';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gerenciar Categorias</h1>
</div>

<div class="row g-4">
    <!-- Coluna do Formulário -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h3 class="h5 mb-0"><i class="bi bi-plus-circle-fill"></i> Adicionar Nova</h3>
            </div>
            <div class="card-body">
                <!-- Alertas de sucesso ou erro -->
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>
                <?php if ($sucesso): ?>
                    <div class="alert alert-success"><?php echo $sucesso; ?></div>
                <?php endif; ?>

                <form action="categorias.php" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nome_categoria" name="nome_categoria" placeholder="Nome da Categoria" required>
                        <label for="nome_categoria">Nome da Categoria</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save-fill"></i> Salvar Categoria
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Coluna da Tabela -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h3 class="h5 mb-0"><i class="bi bi-list-ul"></i> Categorias Existentes</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-4">ID</th>
                                <th scope="col">Nome</th>
                                <?php if ($_SESSION['role'] === 'admin_master'): ?>
                                    <th scope="col">Loja</th>
                                <?php endif; ?>
                                <th scope="col" class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($categorias) && $categorias): ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td class="ps-4"><?php echo $categoria['id']; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($categoria['nome']); ?></td>
                                        
                                        <?php if ($_SESSION['role'] === 'admin_master'): ?>
                                            <td><?php echo htmlspecialchars($categoria['nome_loja']); ?></td>
                                        <?php endif; ?>
                                        
                                        <td class="text-end pe-4">
                                            <a href="editar_categoria.php?id=<?php echo $categoria['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <form action="excluir_categoria.php" method="POST" class="d-inline" onsubmit="return confirm('Atenção: excluir esta categoria fará com que todos os produtos nela sejam listados como SEM CATEGORIA. Deseja continuar?');">
                                                <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo ($_SESSION['role'] === 'admin_master') ? '4' : '3'; ?>" class="text-center py-5 text-muted">
                                        <i class="bi bi-tags fs-1 d-block mb-2"></i>
                                        Nenhuma categoria encontrada.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>