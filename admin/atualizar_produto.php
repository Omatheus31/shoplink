<?php
// Inclui a conexão com o banco de dados
require_once '../config/database.php';

// 1. VERIFICAR SE O FORMULÁRIO FOI SUBMETIDO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. COLETAR OS DADOS DO FORMULÁRIO (INCLUINDO O ID)
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(',', '.', $_POST['preco']);

    // Validação básica
    if (empty($id) || empty($nome) || empty($preco)) {
        die("Erro: Dados essenciais não foram enviados.");
    }

    try {
        // --- LÓGICA DE UPLOAD DA NOVA IMAGEM (SE HOUVER) ---
        $nova_imagem_url = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            
            // Pega o nome da imagem antiga para deletá-la depois
            $stmt_old_img = $pdo->prepare("SELECT imagem_url FROM produtos WHERE id = :id");
            $stmt_old_img->execute([':id' => $id]);
            $imagem_antiga = $stmt_old_img->fetchColumn();

            // Processa o upload da nova imagem
            $target_dir = "../uploads/";
            $imageFileType = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $nova_imagem_url = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $nova_imagem_url;

            if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
                die("Erro ao fazer upload da nova imagem.");
            }
        }

        // 3. CONSTRUIR A QUERY SQL DINAMICAMENTE
        if ($nova_imagem_url) {
            // Se uma nova imagem foi enviada, atualiza todos os campos, incluindo a imagem
            $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, imagem_url = :imagem_url WHERE id = :id";
        } else {
            // Se nenhuma nova imagem foi enviada, atualiza apenas os campos de texto
            $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco WHERE id = :id";
        }
        
        // 4. PREPARAR E EXECUTAR A QUERY
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindValue(':descricao', ''.$descricao);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Se houver uma nova imagem, vincula o parâmetro
        if ($nova_imagem_url) {
            $stmt->bindParam(':imagem_url', $nova_imagem_url);
        }
        
        $stmt->execute();
        
        // Se uma nova imagem foi enviada com sucesso e o BD atualizado, apaga a antiga
        if ($nova_imagem_url && !empty($imagem_antiga) && file_exists("../uploads/" . $imagem_antiga)) {
            unlink("../uploads/" . $imagem_antiga);
        }
        
        // 5. REDIRECIONAR PARA A LISTA DE PRODUTOS
        header("Location: produtos.php?status=editado");
        exit();

    } catch (PDOException $e) {
        die("Erro ao atualizar o produto: " . $e->getMessage());
    }

} else {
    // Redireciona se o acesso for direto (sem ser via POST)
    header("Location: produtos.php");
    exit();
}
?>