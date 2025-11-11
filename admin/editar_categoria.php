<?php
// 1. O Guardião: Nos dá o $id_usuario_logado
require_once 'verifica_login.php'; 
require_once '../config/database.php';

// 1. VERIFICAR SE O ID FOI PASSADO PELA URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 2. BUSCAR A CATEGORIA NO BANCO DE DADOS
        // --- MUDANÇA AQUI ---
        // Agora, só busca o ID se ele pertencer ao usuário logado
        $sql = "SELECT * FROM categorias WHERE id = :id AND id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':id_usuario' => $id_usuario_logado
        ]);
        $categoria = $stmt->fetch();

        // Se a categoria não for encontrada (ou não pertencer ao usuário),
        // o $categoria será false.
        if (!$categoria) {
            header("Location: categorias.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Erro ao buscar a categoria: ". $e->getMessage());
    }
} else {
    header("Location: categorias.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container { background-color: #fff; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .form-container input[type="text"] { width: 80%; padding: 10px; }
        .form-container button { padding: 10px 15px; background-color: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <header class="main-header" style="padding: 15px; margin-bottom: 0;">
        <h1>Painel de Administração</h1>
        <nav>
            <a href="categorias.php" style="color: white;">Voltar para Categorias</a>
            <a href="../logout.php" style="color: #ffcccc; margin-left: auto;">Sair</a>
        </nav>
    </header>

    <main class="container">
        <h2>Editar Categoria</h2>

        <div class="form-container">
            <form action="atualizar_categoria.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                
                <label for="nome_categoria">Nome:</label>
                <input type="text" id="nome_categoria" name="nome_categoria" 
                       value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
                
                <button type="submit">Atualizar Categoria</button>
            </form>
        </div>
    </main>
</body>
</html>