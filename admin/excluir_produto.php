<?php
// Inclui a conexão com o banco de dados
require_once '../config/database.php';

// 1. VERIFICAR SE A REQUISIÇÃO É DO TIPO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. PEGAR O ID DO PRODUTO ENVIADO PELO FORMULÁRIO OCULTO
    $id = $_POST['id'];

    // Validação básica do ID
    if (empty($id)) {
        die("Erro: ID do produto não fornecido.");
    }

    try {
        // --- ETAPA CRÍTICA: APAGAR O ARQUIVO DA IMAGEM ---
        
        // 3. Primeiro, buscar o nome do arquivo da imagem no banco de dados
        $stmt_select_img = $pdo->prepare("SELECT imagem_url FROM produtos WHERE id = :id");
        $stmt_select_img->execute([':id' => $id]);
        $imagem_url = $stmt_select_img->fetchColumn();

        // 4. Se o produto existir e tiver uma imagem, apagar o arquivo físico
        if ($imagem_url && file_exists("../uploads/" . $imagem_url)) {
            unlink("../uploads/" . $imagem_url);
        }

        // --- ETAPA FINAL: APAGAR O REGISTRO DO BANCO DE DADOS ---
        
        // 5. Preparar e executar a query DELETE
        $sql = "DELETE FROM produtos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // 6. REDIRECIONAR PARA A LISTA DE PRODUTOS COM UMA MENSAGEM DE SUCESSO
        header("Location: produtos.php?status=excluido");
        exit();

    } catch (PDOException $e) {
        die("Erro ao excluir o produto: " . $e->getMessage());
    }

} else {
    // Se o acesso não for via POST, redireciona para a página principal
    header("Location: produtos.php");
    exit();
}
?>