<?php
// 1. O Guardião: Nos dá o $id_usuario_logado
require_once 'verifica_login.php'; 
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : NULL;

    if (empty($nome) || empty($preco) || !isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        die("Erro: Todos os campos, incluindo a imagem, são obrigatórios.");
    }
    
    // ... (Lógica de upload de imagem não muda) ...
    $target_dir = "../uploads/";
    $imageFileType = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $nome_arquivo = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $nome_arquivo;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
        
        try {
            // --- MUDANÇA NA QUERY SQL ---
            // Adicionamos o id_usuario
            $sql = "INSERT INTO produtos (nome, descricao, preco, id_categoria, imagem_url, id_usuario) 
                    VALUES (:nome, :descricao, :preco, :id_categoria, :imagem_url, :id_usuario)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':id_categoria', $id_categoria);
            $stmt->bindParam(':imagem_url', $nome_arquivo);
            // --- NOVA LINHA ---
            $stmt->bindParam(':id_usuario', $id_usuario_logado); // Pega o ID da sessão

            $stmt->execute();
            
            header("Location: adicionar_produto.php?status=sucesso");
            exit();

        } catch (PDOException $e) {
            die("Erro ao salvar o produto no banco de dados: " . $e->getMessage());
        }

    } else {
        die("Desculpe, houve um erro ao fazer o upload da sua imagem.");
    }
} else {
    header("Location: adicionar_produto.php");
    exit();
}
?>