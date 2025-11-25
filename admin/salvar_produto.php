<?php
// admin/salvar_produto.php
session_start();

// 1. VERIFICAÇÃO DE SEGURANÇA MANUAL (Sem incluir o header visual)
if (!isset($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(',', '.', $_POST['preco']); // Troca vírgula por ponto
    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : NULL;

    // Validação simples
    if (empty($nome) || empty($preco) || !isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        // Poderia redirecionar com erro, mas vamos simplificar
        die("Erro: Preencha todos os campos e selecione uma imagem.");
    }
    
    // Upload da Imagem
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $nome_arquivo = uniqid() . '.' . $extensao;
    $target_file = $target_dir . $nome_arquivo;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
        try {
            $sql = "INSERT INTO produtos (nome, descricao, preco, id_categoria, imagem_url) 
                    VALUES (:nome, :descricao, :preco, :id_categoria, :img)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':preco' => $preco,
                ':id_categoria' => $id_categoria,
                ':img' => $nome_arquivo
            ]);
            
            // Redirecionamento Limpo
            header("Location: produtos.php?sucesso=1");
            exit();

        } catch (PDOException $e) {
            die("Erro banco: " . $e->getMessage());
        }
    } else {
        die("Erro ao mover arquivo de imagem.");
    }
}
?>