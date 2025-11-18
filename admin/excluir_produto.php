<?php
// 1. INCLUI O HEADER DO ADMIN (Protege, conecta ao $pdo, nos dá $id_usuario_logado)
require_once 'includes/header_admin.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    if (empty($id)) {
        die("Erro: ID do produto não fornecido.");
    }

    try {
        // --- ETAPA 1: APAGAR O ARQUIVO DA IMAGEM ---
        
        $sql_img = "SELECT imagem_url FROM produtos WHERE id = :id";
        $params_img = [':id' => $id];
        
        // Admin Loja SÓ PODE buscar a imagem de um produto seu
        if ($_SESSION['role'] === 'admin_loja') {
            $sql_img .= " AND id_usuario = :id_usuario";
            $params_img[':id_usuario'] = $id_usuario_logado;
        }

        $stmt_select_img = $pdo->prepare($sql_img);
        $stmt_select_img->execute($params_img);
        $imagem_url = $stmt_select_img->fetchColumn();

        if ($imagem_url === false) {
            // Tentativa de excluir produto que não é dele
            header("Location: produtos.php");
            exit();
        }

        if ($imagem_url && file_exists("../uploads/" . $imagem_url)) {
            unlink("../uploads/" . $imagem_url);
        }

        // --- ETAPA 2: APAGAR O REGISTRO DO BANCO ---
        
        $sql_delete = "DELETE FROM produtos WHERE id = :id";
        $params_delete = [':id' => $id];
        
        // Admin Loja SÓ PODE excluir o seu
        if ($_SESSION['role'] === 'admin_loja') {
            $sql_delete .= " AND id_usuario = :id_usuario";
            $params_delete[':id_usuario'] = $id_usuario_logado;
        }
        
        $stmt = $pdo->prepare($sql_delete);
        $stmt->execute($params_delete);
        
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