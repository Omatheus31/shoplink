<?php
// admin/salvar_produto.php
require_once 'includes/header_admin.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : NULL;

    if (empty($nome) || empty($preco) || !isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        die("Erro: Todos os campos, incluindo a imagem, são obrigatórios.");
    }
    
    $target_dir = "../uploads/";
    // Verifica se a pasta existe, se não, cria (segurança extra)
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $imageFileType = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $nome_arquivo = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $nome_arquivo;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
        
        try {
            // REMOVIDO: id_usuario da query
            $sql = "INSERT INTO produtos (nome, descricao, preco, id_categoria, imagem_url) 
                    VALUES (:nome, :descricao, :preco, :id_categoria, :imagem_url)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':id_categoria', $id_categoria);
            $stmt->bindParam(':imagem_url', $nome_arquivo);
            // REMOVIDO: bindParam de id_usuario

            $stmt->execute();
            
            header("Location: adicionar_produto.php?status=sucesso");
            exit();

        } catch (PDOException $e) {
            die("Erro ao salvar o produto no banco de dados: " . $e->getMessage());
        }

    } else {
        die("Desculpe, houve um erro ao fazer o upload da sua imagem. Verifique as permissões da pasta 'uploads'.");
    }
} else {
    header("Location: adicionar_produto.php");
    exit();
}
?>