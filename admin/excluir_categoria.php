<?php
// 1. INCLUI O HEADER DO ADMIN (Protege a página, nos dá $pdo e $id_usuario_logado)
require_once 'includes/header_admin.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    if (empty($id)) {
        die("Erro: ID não fornecido.");
    }

    try {
        // Lógica de 3 Papéis:
        // Admin Master pode excluir qualquer categoria
        // Admin Loja só pode excluir a sua
        
        $sql = "DELETE FROM categorias WHERE id = :id";
        $params = [':id' => $id];

        if ($_SESSION['role'] === 'admin_loja') {
            $sql .= " AND id_usuario = :id_usuario";
            $params[':id_usuario'] = $id_usuario_logado;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
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