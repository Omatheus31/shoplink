<?php
// 1. Incluir o arquivo de conexão
require_once '../config/database.php';

// 2. Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. Coletar e validar os dados do formulário
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(',', '.', $_POST['preco']);
    
    // --- MUDANÇA AQUI ---
    // Pega o id_categoria. Se for uma string vazia "", converte para NULL
    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : NULL;
    // --- FIM DA MUDANÇA ---

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
            $sql = "INSERT INTO produtos (nome, descricao, preco, id_categoria, imagem_url) VALUES (:nome, :descricao, :preco, :id_categoria, :imagem_url)";
            $stmt = $pdo->prepare($sql);
            
            // 8. Vincular os parâmetros
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':id_categoria', $id_categoria); // --- NOVA LINHA ---
            $stmt->bindParam(':imagem_url', $nome_arquivo);

            // 9. Executar
            $stmt->execute();
            
            // 10. Redirecionar
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