<?php
// 1. INCLUI O HEADER DO ADMIN (Protege, conecta ao $pdo, nos dá $id_usuario_logado)
require_once 'includes/header_admin.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. COLETA OS DADOS (Já tínhamos feito isso)
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : NULL;

    if (empty($nome) || empty($preco) || !isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        die("Erro: Todos os campos, incluindo a imagem, são obrigatórios.");
    }
    
    // 3. LÓGICA DE UPLOAD (Não muda)
    $target_dir = "../uploads/";
    $imageFileType = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $nome_arquivo = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $nome_arquivo;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
        
        try {
            // 4. LÓGICA DE SALVAMENTO (Já usa $id_usuario_logado)
            $sql = "INSERT INTO produtos (nome, descricao, preco, id_categoria, imagem_url, id_usuario) 
                    VALUES (:nome, :descricao, :preco, :id_categoria, :imagem_url, :id_usuario)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':id_categoria', $id_categoria);
            $stmt->bindParam(':imagem_url', $nome_arquivo);
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