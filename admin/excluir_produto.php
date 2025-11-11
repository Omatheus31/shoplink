<?php
// 1. O Guardião: Nos dá o $id_usuario_logado
require_once 'verifica_login.php'; 
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    if (empty($id)) {
        die("Erro: ID do produto não fornecido.");
    }

    try {
        // --- ETAPA CRÍTICA: APAGAR O ARQUIVO DA IMAGEM ---
        
        // 3. Busca o nome da imagem, mas SÓ SE o produto pertencer ao usuário
        $stmt_select_img = $pdo->prepare("SELECT imagem_url FROM produtos WHERE id = :id AND id_usuario = :id_usuario");
        $stmt_select_img->execute([
            ':id' => $id,
            ':id_usuario' => $id_usuario_logado
        ]);
        $imagem_url = $stmt_select_img->fetchColumn();

        // 4. Se encontrou a imagem (ou seja, o produto é do usuário), apaga o arquivo
        if ($imagem_url && file_exists("../uploads/" . $imagem_url)) {
            unlink("../uploads/" . $imagem_url);
        }

        // --- ETAPA FINAL: APAGAR O REGISTRO DO BANCO ---
        
        // 5. Deleta o produto SÓ SE ele pertencer ao usuário
        $sql = "DELETE FROM produtos WHERE id = :id AND id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':id_usuario' => $id_usuario_logado
        ]);
        
        header("Location: produtos.php?status=excluido");
        exit();

    } catch (PDOException $e) {
        die("Erro ao excluir o produto: " . $e->getMessage());
    }
} else {
    header("Location: produtos.php");
    exit();
}
?>