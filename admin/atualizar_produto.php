<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // COLETAR OS DADOS (INCLUINDO A CATEGORIA)
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(',', '.', $_POST['preco']);
    
    // --- MUDANÇA AQUI ---
    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : NULL;
    // --- FIM DA MUDANÇA ---

    if (empty($id) || empty($nome) || empty($preco)) {
        die("Erro: Dados essenciais não foram enviados.");
    }

    try {
        // ... (Lógica de upload de imagem não muda) ...
        $nova_imagem_url = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            
            $stmt_old_img = $pdo->prepare("SELECT imagem_url FROM produtos WHERE id = :id");
            $stmt_old_img->execute([':id' => $id]);
            $imagem_antiga = $stmt_old_img->fetchColumn();

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
            // Se uma nova imagem foi enviada, atualiza TUDO
            $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, id_categoria = :id_categoria, imagem_url = :imagem_url WHERE id = :id";
        } else {
            // Se não, atualiza tudo MENOS a imagem
            $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, id_categoria = :id_categoria WHERE id = :id";
        }
        
        // 4. PREPARAR E EXECUTAR A QUERY
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindValue(':descricao', ''.$descricao); // Usando bindValue como corrigimos
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':id_categoria', $id_categoria); // --- NOVA LINHA ---
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($nova_imagem_url) {
            $stmt->bindParam(':imagem_url', $nova_imagem_url);
        }
        
        $stmt->execute();
        
        if ($nova_imagem_url && !empty($imagem_antiga) && file_exists("../uploads/" . $imagem_antiga)) {
            unlink("../uploads/" . $imagem_antiga);
        }
        
        // 5. REDIRECIONAR PARA A LISTA (COM MENSAGEM DE SUCESSO)
        header("Location: produtos.php?status=editado"); // Vamos usar isso para o alerta
        exit();

    } catch (PDOException $e) {
        die("Erro ao atualizar o produto: " . $e->getMessage());
    }

} else {
    header("Location: produtos.php");
    exit();
}
?>