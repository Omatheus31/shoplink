<?php
// admin/excluir_produto.php
session_start();

// 1. Verifica Admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    try {
        // 1. Busca a imagem para apagar do servidor
        $stmt = $pdo->prepare("SELECT imagem_url FROM produtos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $produto = $stmt->fetch();

        // 2. Se tiver imagem, apaga o arquivo físico
        if ($produto && !empty($produto['imagem_url'])) {
            $caminho_imagem = "../uploads/" . $produto['imagem_url'];
            if (file_exists($caminho_imagem)) {
                unlink($caminho_imagem); // Deleta o arquivo
            }
        }

        // 3. Deleta o registro do banco
        // REMOVIDO: AND id_usuario = ... (Agora deleta direto pelo ID)
        $stmt_del = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
        $stmt_del->execute([':id' => $id]);

        header("Location: produtos.php?msg=deletado");
        exit();

    } catch (PDOException $e) {
        die("Erro ao excluir: " . $e->getMessage());
    }
} else {
    header("Location: produtos.php");
    exit();
}
?>