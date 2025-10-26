<?php
require_once '../config/database.php';

// 1. VERIFICAR SE A REQUISIÇÃO É DO TIPO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. PEGAR O ID
    $id = $_POST['id'];

    if (empty($id)) {
        die("Erro: ID não fornecido.");
    }

    try {
        // 3. EXECUTAR O DELETE
        // Graças ao 'ON DELETE SET NULL', não precisamos nos preocupar
        // em atualizar os produtos manualmente. O BD faz isso.
        $sql = "DELETE FROM categorias WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // 4. REDIRECIONAR
        header("Location: categorias.php?status=excluida");
        exit();

    } catch (PDOException $e) {
        die("Erro ao excluir categoria: " . $e->getMessage());
    }
} else {
    header("Location: categorias.php");
    exit();
}
?>