<?php
// admin/excluir_cliente.php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        // Deleta apenas se for CLIENTE (segurança para não apagar admin por engano)
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id AND role = 'cliente'");
        $stmt->execute([':id' => $id]);
        header("Location: clientes.php?msg=deletado");
    } catch (PDOException $e) {
        die("Erro: " . $e->getMessage());
    }
}
?>