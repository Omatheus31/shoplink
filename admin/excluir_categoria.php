<?php
// 1. O Guardião: Nos dá o $id_usuario_logado
require_once 'verifica_login.php'; 
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    if (empty($id)) {
        die("Erro: ID não fornecido.");
    }

    try {
        // 3. EXECUTAR O DELETE
        // --- MUDANÇA AQUI ---
        // Adicionamos "AND id_usuario = :id_usuario" ao WHERE
        // Garante que um usuário só possa EXCLUIR suas PRÓPRIAS categorias.
        $sql = "DELETE FROM categorias WHERE id = :id AND id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':id_usuario' => $id_usuario_logado
        ]);
        
        header("Location: categorias.php?status=excluida");
        exit();

    } catch (PDOException $e) {
        die("Erro ao excluir categoria: ". $e->getMessage());
    }
} else {
    header("Location: categorias.php");
    exit();
}
?>