<?php
// 1. Incluir o arquivo de conexão com o banco de dados
require_once '../config/database.php';

// 2. Verificar se o formulário foi submetido (se a requisição é do tipo POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. Coletar e validar os dados do formulário
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    // Converte o preço de '1,23' para '1.23' para salvar no banco
    $preco = str_replace(',', '.', $_POST['preco']); 

    // Validação simples (apenas para o protótipo)
    if (empty($nome) || empty($preco) || !isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        die("Erro: Todos os campos, incluindo a imagem, são obrigatórios.");
    }
    
    // --- LÓGICA DE UPLOAD DA IMAGEM ---

    // 4. Definir o diretório de destino para as imagens
    $target_dir = "../uploads/";

    // 5. Criar um nome de arquivo único para evitar sobreposição
    // basename() extrai o nome do arquivo, ex: "minha-foto.jpg"
    $imageFileType = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $nome_arquivo = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $nome_arquivo;

    // 6. Mover o arquivo temporário para o diretório de destino
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
        // Se o upload foi bem-sucedido, continue para salvar no banco
        
        // --- LÓGICA DE INSERÇÃO NO BANCO DE DADOS ---
        
        try {
            // 7. Preparar a query SQL para evitar SQL Injection
            $sql = "INSERT INTO produtos (nome, descricao, preco, imagem_url) VALUES (:nome, :descricao, :preco, :imagem_url)";
            $stmt = $pdo->prepare($sql);
            
            // 8. Vincular os parâmetros da query com os valores recebidos
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':imagem_url', $nome_arquivo); // Salvamos apenas o nome do arquivo

            // 9. Executar a query
            $stmt->execute();
            
            // 10. Redirecionar o usuário para a página de adicionar, com uma mensagem de sucesso
            // (No futuro, podemos redirecionar para a lista de produtos)
            header("Location: adicionar_produto.php?status=sucesso");
            exit();

        } catch (PDOException $e) {
            // Em caso de erro, exibir a mensagem (em produção, logar o erro)
            die("Erro ao salvar o produto no banco de dados: " . $e->getMessage());
        }

    } else {
        // Se houve um erro no upload do arquivo
        die("Desculpe, houve um erro ao fazer o upload da sua imagem.");
    }
} else {
    // Se alguém tentar acessar o arquivo diretamente sem enviar o formulário
    header("Location: adicionar_produto.php");
    exit();
}
?>