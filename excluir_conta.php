<?php
session_start();
require_once 'config/database.php';
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$id = $_SESSION['id_usuario'];

// Se for GET: mostra confirmação
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $titulo_pagina = 'Excluir Conta';
    require_once 'includes/header_public.php';
    ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <h3>Confirmar exclusão da conta</h3>
            <p>Tem certeza que deseja excluir permanentemente sua conta? Esta ação não pode ser desfeita.</p>
            <form method="post" action="excluir_conta.php">
                <button type="submit" class="btn btn-danger">Excluir minha conta</button>
                <a href="minha_conta.php" class="btn btn-outline-secondary">Cancelar</a>
            </form>
        </div>
    </div>
    <?php
    require_once 'includes/footer_public.php';
    exit();
}

// Se chegou aqui via POST -> executa exclusão
try {
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $id]);
    session_destroy();
    header('Location: index.php?msg=conta_excluida');
    exit();
} catch (PDOException $e) {
    die('Erro ao excluir conta: ' . $e->getMessage());
}
?>