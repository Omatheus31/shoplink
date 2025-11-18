<?php
// 1. INCLUI O HEADER DO ADMIN (Protege, conecta ao $pdo, nos dá $id_usuario_logado)
require_once 'includes/header_admin.php'; 

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
        // 2. LÓGICA DE UPLOAD (com segurança de 3 papéis)
        $nova_imagem_url = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            
            $sql_old_img = "SELECT imagem_url FROM produtos WHERE id = :id";
            $params_old_img = [':id' => $id];
            
            // Admin Loja SÓ PODE buscar (para apagar) a imagem de um produto seu
            if ($_SESSION['role'] === 'admin_loja') {
                $sql_old_img .= " AND id_usuario = :id_usuario";
                $params_old_img[':id_usuario'] = $id_usuario_logado;
            }

            $stmt_old_img = $pdo->prepare($sql_old_img);
            $stmt_old_img->execute($params_old_img);
            $imagem_antiga = $stmt_old_img->fetchColumn();
            
            // Se $imagem_antiga for false, significa que o admin_loja tentou
            // editar um produto que não é dele, E TENTOU UPAR UMA IMAGEM.
            // (Embora o form de update já devesse bloquear isso, é uma dupla checagem)
            if ($imagem_antiga === false) {
                 header("Location: produtos.php"); // Redireciona por segurança
                 exit();
            }

            // Processa o upload
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
            $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, id_categoria = :id_categoria, imagem_url = :imagem_url 
                    WHERE id = :id";
        } else {
            $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, id_categoria = :id_categoria 
                    WHERE id = :id";
        }
        
        // 4. ADICIONAR FILTRO DE PERMISSÃO (3 PAPÉIS)
        $params = [
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':preco' => $preco,
            ':id_categoria' => $id_categoria,
            ':id' => $id,
        ];

        if ($nova_imagem_url) {
            $params[':imagem_url'] = $nova_imagem_url;
        }

        // Admin Loja SÓ PODE atualizar o seu
        if ($_SESSION['role'] === 'admin_loja') {
            $sql .= " AND id_usuario = :id_usuario";
            $params[':id_usuario'] = $id_usuario_logado;
        }
        // Admin Master pode atualizar qualquer um
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // 5. APAGAR IMAGEM ANTIGA
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