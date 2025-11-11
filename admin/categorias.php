<?php
// 1. O Guardião: Inicia a sessão e nos dá o $id_usuario_logado
require_once 'verifica_login.php'; 

// 2. Conexão com o banco
require_once '../config/database.php';

// PARTE 1: Processar o formulário de NOVA CATEGORIA (se enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_categoria'])) {
    $nome_categoria = trim($_POST['nome_categoria']);
    
    if (!empty($nome_categoria)) {
        try {
            // --- MUDANÇA AQUI ---
            // Agora também inserimos o id_usuario do usuário logado
            $sql_insert = "INSERT INTO categorias (nome, id_usuario) VALUES (:nome, :id_usuario)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([
                ':nome' => $nome_categoria,
                ':id_usuario' => $id_usuario_logado // Variável vinda do verifica_login.php
            ]);
            
            header("Location: categorias.php?status=criada");
            exit();
        } catch (PDOException $e) {
            // (Tratamento de erro não muda)
            if ($e->getCode() == 23000) {
                $erro = "Erro: Esta categoria já existe.";
            } else {
                $erro = "Erro ao salvar categoria: " . $e->getMessage();
            }
        }
    } else {
        $erro = "O nome da categoria não pode estar vazio.";
    }
}

// PARTE 2: Buscar todas as categorias existentes para listar
try {
    // --- MUDANÇA AQUI ---
    // Agora selecionamos APENAS as categorias ONDE o id_usuario é o do logado
    $query = "SELECT * FROM categorias WHERE id_usuario = :id_usuario ORDER BY nome ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id_usuario' => $id_usuario_logado]); // Passamos o ID para a query
    $categorias = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Erro ao buscar categorias: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .admin-table th { background-color: #f2f2f2; }
        .admin-table tr:nth-child(even) { background-color: #f9f9f9; }
        
        .action-btn { padding: 5px 10px; text-decoration: none; border-radius: 4px; color: white; margin-right: 5px; font-size: 0.9em; }
        .edit-btn { background-color: #3498db; }
        .delete-btn { background-color: #e74c3c; border: none; cursor: pointer; font-family: inherit; }

        .form-container { background-color: #fff; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .form-container input[type="text"] { width: 80%; padding: 10px; }
        .form-container button { padding: 10px 15px; background-color: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <header class="main-header" style="padding: 15px; margin-bottom: 0;">
        <h1>Painel de Administração</h1>
        <nav>
            <a href="index.php" style="color: white; margin-right: 15px;">Dashboard</a>
            <a href="pedidos.php" style="color: white; margin-right: 15px;">Pedidos</a>
            <a href="produtos.php" style="color: white; margin-right: 15px;">Produtos</a>
            <a href="categorias.php" style="color: white; margin-right: 15px; font-weight: bold;">Categorias</a>
            <a href="adicionar_produto.php" style="color: white; margin-right: 15px;">Adicionar Produto</a>
            <a href="../logout.php" style="color: #ffcccc; margin-right: 15px;">Sair</a>
        </nav>
    </header>

    <main class="container">
        <h2>Gerenciar Categorias</h2>

        <div class="form-container">
            <h3>Adicionar Nova Categoria</h3>
            <?php if (isset($erro)): ?>
                <p style="color: red;"><?php echo $erro; ?></p>
            <?php endif; ?>
            <form action="categorias.php" method="POST">
                <label for="nome_categoria">Nome:</label>
                <input type="text" id="nome_categoria" name="nome_categoria" required>
                <button type="submit">Salvar Categoria</button>
            </form>
        </div>

        <h3>Categorias Existentes</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($categorias): ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?php echo $categoria['id']; ?></td>
                            <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                            <td>
                                <a href="editar_categoria.php?id=<?php echo $categoria['id']; ?>" class="action-btn edit-btn">Editar</a>
                                
                                <form action="excluir_categoria.php" method="POST" style="display: inline;" onsubmit="return confirm('Atenção: excluir esta categoria fará com que todos os produtos nela sejam listados como SEM CATEGORIA. Deseja continuar?');">
                                    <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                                    <button type="submit" class="action-btn delete-btn">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Nenhuma categoria encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>