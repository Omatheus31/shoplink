<?php
// admin/editar_cliente.php
session_start();

// 1. Lógica PHP antes do HTML
if (!isset($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: clientes.php");
    exit();
}

$id = (int)$_GET['id'];

// PROCESSAMENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "UPDATE usuarios SET nome=:nome, email=:email, telefone=:tel WHERE id=:id AND role='cliente'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $_POST['nome'],
            ':email' => $_POST['email'],
            ':tel' => $_POST['telefone'],
            ':id' => $id
        ]);
        
        // Redireciona para a lista
        header("Location: clientes.php?msg=atualizado");
        exit();

    } catch (PDOException $e) {
        $erro_msg = "Erro: " . $e->getMessage();
    }
}

// BUSCA DADOS
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id=:id AND role='cliente'");
$stmt->execute([':id' => $id]);
$cliente = $stmt->fetch();

if(!$cliente) { header("Location: clientes.php"); exit; }

// 2. INÍCIO DO HTML
$titulo_pagina = "Editar Cliente";
require_once 'includes/header_admin.php';
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Cliente</h1>
    <a href="clientes.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<?php if(isset($erro_msg)) echo "<div class='alert alert-danger'>$erro_msg</div>"; ?>

<div class="card shadow-sm border-0" style="max-width: 600px; margin: 0 auto;">
    <div class="card-body p-4">
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($cliente['telefone']); ?>">
            </div>
            <button class="btn btn-primary w-100">Salvar Alterações</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>