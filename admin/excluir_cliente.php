<?php
require_once 'includes/header_admin.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: clientes.php');
    exit();
}

try {
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $id]);
    header('Location: clientes.php?msg=excluido');
    exit();
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Erro ao excluir: ' . $e->getMessage() . '</div>';
}

require_once 'includes/footer_admin.php';
