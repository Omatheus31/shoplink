<?php
// 1. O Guardião: Nos dá o $id_usuario_logado
require_once 'verifica_login.php'; 
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = trim($_POST['nome_categoria']);

    if (empty($id) || empty($nome)) {
        die("Erro: Dados incompletos.");
    }

    try {
        // 3. EXECUTAR O UPDATE NO BANCO
        // --- MUDANÇA AQUI ---
        // Adicionamos "AND id_usuario = :id_usuario" ao WHERE
        // Isso garante que um usuário só possa ATUALIZAR suas PRÓPRIAS categorias.
        $sql = "UPDATE categorias SET nome = :nome WHERE id = :id AND id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':id'   => $id,
            ':id_usuario' => $id_usuario_logado
        ]);
        
        header("Location: categorias.php?status=editada");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            die("Erro: Já existe uma categoria com este nome. <a href='javascript:history.back()'>Voltar</a>");
        } else {
            die("Erro ao atualizar categoria: ". $e->getMessage());
        }
    }
} else {
    header("Location: categorias.php");
    exit();
}
?>