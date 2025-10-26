<?php
require_once '../config/database.php';

// 1. VERIFICAR SE A REQUISIÇÃO É DO TIPO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. COLETAR OS DADOS DO FORMULÁRIO
    $id = $_POST['id'];
    $nome = trim($_POST['nome_categoria']);

    if (empty($id) || empty($nome)) {
        die("Erro: Dados incompletos.");
    }

    try {
        // 3. EXECUTAR O UPDATE NO BANCO
        $sql = "UPDATE categorias SET nome = :nome WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':id'   => $id
        ]);
        
        // 4. REDIRECIONAR DE VOLTA PARA A LISTA
        header("Location: categorias.php?status=editada");
        exit();

    } catch (PDOException $e) {
        // Trata erro de nome duplicado
        if ($e->getCode() == 23000) {
            die("Erro: Já existe uma categoria com este nome. <a href='javascript:history.back()'>Voltar</a>");
        } else {
            die("Erro ao atualizar categoria: " . $e->getMessage());
        }
    }
} else {
    header("Location: categorias.php");
    exit();
}
?>