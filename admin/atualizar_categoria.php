<?php
// 1. INCLUI O HEADER DO ADMIN (Protege a página, nos dá $pdo e $id_usuario_logado)
require_once 'includes/header_admin.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = trim($_POST['nome_categoria']);

    if (empty($id) || empty($nome)) {
        die("Erro: Dados incompletos.");
    }

    try {
        // Lógica de 3 Papéis:
        // Admin Master pode editar qualquer categoria
        // Admin Loja só pode editar a sua
        
        $sql = "UPDATE categorias SET nome = :nome WHERE id = :id";
        $params = [
            ':nome' => $nome,
            ':id'   => $id
        ];

        if ($_SESSION['role'] === 'admin_loja') {
            $sql .= " AND id_usuario = :id_usuario";
            $params[':id_usuario'] = $id_usuario_logado;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
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