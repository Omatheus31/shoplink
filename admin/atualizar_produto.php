<?php
// 1. O Guardião: Nos dá o $id_usuario_logado
require_once 'verifica_login.php'; 
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : NULL;

    if (empty($id) || empty($nome) || empty($preco)) {
        die("Erro: Dados essenciais não foram enviados.");
    }

    try {
        // ... (Lógica de upload de imagem não muda) ...
        $nova_imagem_url = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            
            $stmt_old_img = $pdo->prepare("SELECT imagem_url FROM produtos WHERE id = :id AND id_usuario = :id_usuario");
            $stmt_old_img->execute([':id' => $id, ':id_usuario' => $id_usuario_logado]);
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
            $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, id_categoria = :id_categoria, imagem_url = :imagem_url 
                    WHERE id = :id AND id_usuario = :id_usuario"; // --- MUDANÇA AQUI ---
        } else {
            // Se não, atualiza tudo MENOS a imagem
            $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, id_categoria = :id_categoria 
                    WHERE id = :id AND id_usuario = :id_usuario"; // --- MUDANÇA AQUI ---
        }
        
        // 4. PREPARAR E EXECUTAR A QUERY
        $stmt = $pdo->prepare($sql);
        
        $params = [
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':preco' => $preco,
            ':id_categoria' => $id_categoria,
            ':id' => $id,
            ':id_usuario' => $id_usuario_logado // --- MUDANÇA AQUI ---
        ];

        if ($nova_imagem_url) {
            $params[':imagem_url'] = $nova_imagem_url;
        }
        
        $stmt->execute($params);
        
        if ($nova_imagem_url && !empty($imagem_antiga) && file_exists("../uploads/" . $imagem_antiga)) {
            unlink("../uploads/" . $imagem_antiga);
        }
        
        header("Location: produtos.php?status=editado");
        exit();

    } catch (PDOException $e) {
        die("Erro ao atualizar o produto: " . $e->getMessage());
    }
} else {
    header("Location: produtos.php");
    exit();
}
?>