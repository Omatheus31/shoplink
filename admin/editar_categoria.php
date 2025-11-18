<?php
// 1. INCLUI O HEADER DO ADMIN
$titulo_pagina = "Editar Categoria"; 
require_once 'includes/header_admin.php';

// 2. BUSCA DA CATEGORIA (com segurança de 3 papéis)
if (isset($_GET['id'])) {
    $id_categoria = (int)$_GET['id'];
    
    $sql_cat = "SELECT * FROM categorias WHERE id = :id_categoria";
    $params = [':id_categoria' => $id_categoria];

    // Admin Loja SÓ PODE editar o seu
    if ($_SESSION['role'] === 'admin_loja') {
        $sql_cat .= " AND id_usuario = :id_usuario";
        $params[':id_usuario'] = $id_usuario_logado;
    }
    
    try {
        $stmt_cat = $pdo->prepare($sql_cat);
        $stmt_cat->execute($params);
        $categoria = $stmt_cat->fetch();

        if (!$categoria) {
            header("Location: categorias.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Erro ao buscar dados: " . $e->getMessage());
    }
} else {
    header("Location: categorias.php");
    exit();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Categoria</h1>
    <a href="categorias.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left-circle-fill"></i> Voltar para Categorias
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <form action="atualizar_categoria.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nome_categoria" name="nome_categoria" 
                               value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
                        <label for="nome_categoria">Nome da Categoria</label>
                    </div>

                    <!-- Se for Admin Master, mostra quem é o dono, mas não deixa editar -->
                    <?php if ($_SESSION['role'] === 'admin_master'): ?>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="dono" 
                                   value="<?php echo htmlspecialchars($categoria['id_usuario']); // No futuro, buscar o nome do usuário ?>" readonly disabled>
                            <label for="dono">ID do Dono (Admin Master)</label>
                        </div>
                    <?php endif; ?>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save-fill"></i> Atualizar Categoria
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>