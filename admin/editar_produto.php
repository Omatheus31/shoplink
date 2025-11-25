<?php
// admin/editar_produto.php

// 1. LÓGICA PHP (Antes de qualquer HTML)
session_start();
require_once '../config/database.php';

// Verifica Admin manualmente (pois ainda não carregamos o header)
if (!isset($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: produtos.php");
    exit();
}

$id = (int)$_GET['id'];
$erro_msg = "";

// --- PROCESSAMENTO DO FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : NULL;

    // Upload de Imagem (Opcional na edição)
    $nova_imagem = false;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $nome_arquivo = uniqid() . '.' . $ext;
        $target_dir = "../uploads/";
        
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_dir . $nome_arquivo)) {
            $nova_imagem = $nome_arquivo;
        }
    }

    try {
        $sql = "UPDATE produtos SET nome=:nome, descricao=:descricao, preco=:preco, id_categoria=:cat";
        $params = [':nome'=>$nome, ':descricao'=>$descricao, ':preco'=>$preco, ':cat'=>$id_categoria, ':id'=>$id];
        
        if ($nova_imagem) {
            $sql .= ", imagem_url=:img";
            $params[':img'] = $nova_imagem;
        }
        
        $sql .= " WHERE id=:id"; 
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // REDIRECIONAMENTO (Agora funciona, pois não há HTML antes)
        header("Location: produtos.php?msg=atualizado");
        exit();

    } catch (PDOException $e) {
        $erro_msg = "Erro ao atualizar: " . $e->getMessage();
    }
}

// 2. AGORA SIM, CARREGAMOS O VISUAL (HTML)
$titulo_pagina = "Editar Produto";
require_once 'includes/header_admin.php';

// Busca dados atuais para preencher o form
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->execute([':id' => $id]);
$produto = $stmt->fetch();

if (!$produto) {
    echo "<div class='alert alert-warning m-3'>Produto não encontrado.</div>";
    require_once 'includes/footer_admin.php';
    exit();
}

// Busca categorias
$cats = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Produto</h1>
    <a href="produtos.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<?php if ($erro_msg): ?>
    <div class="alert alert-danger"><?php echo $erro_msg; ?></div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="post" enctype="multipart/form-data">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nome do Produto</label>
                            <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Preço (R$)</label>
                            <input type="text" name="preco" class="form-control" value="<?php echo number_format($produto['preco'], 2, ',', ''); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Categoria</label>
                        <select name="id_categoria" class="form-select">
                            <option value="">Sem categoria</option>
                            <?php foreach($cats as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $produto['id_categoria'])?'selected':''; ?>>
                                    <?php echo htmlspecialchars($c['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="4"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Imagem Atual</label>
                            <div class="border rounded p-1">
                                <img src="../uploads/<?php echo $produto['imagem_url']; ?>" class="img-fluid rounded">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold">Trocar Imagem</label>
                            <input type="file" name="imagem" class="form-control" accept="image/*">
                            <div class="form-text">Deixe vazio se quiser manter a imagem atual.</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> Salvar Alterações
                        </button>
                        <a href="produtos.php" class="btn btn-light text-muted">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>