<?php
// 1. O Guardião: Protege a página e nos dá o $id_usuario_logado
require_once 'verifica_login.php'; 
require_once '../config/database.php';

try {
    // 2. BUSCAR AS ESTATÍSTICAS
    // Contagem de Pedidos Pendentes
    $sql_pedidos = "SELECT COUNT(*) FROM pedidos WHERE id_usuario = :id_usuario AND status = 'Pendente'";
    $stmt_pedidos = $pdo->prepare($sql_pedidos);
    $stmt_pedidos->execute([':id_usuario' => $id_usuario_logado]);
    $pedidos_pendentes = $stmt_pedidos->fetchColumn();

    // Contagem de Produtos Ativos
    $sql_produtos = "SELECT COUNT(*) FROM produtos WHERE id_usuario = :id_usuario";
    $stmt_produtos = $pdo->prepare($sql_produtos);
    $stmt_produtos->execute([':id_usuario' => $id_usuario_logado]);
    $total_produtos = $stmt_produtos->fetchColumn();

    // Contagem de Categorias
    $sql_categorias = "SELECT COUNT(*) FROM categorias WHERE id_usuario = :id_usuario";
    $stmt_categorias = $pdo->prepare($sql_categorias);
    $stmt_categorias->execute([':id_usuario' => $id_usuario_logado]);
    $total_categorias = $stmt_categorias->fetchColumn();

} catch (PDOException $e) {
    die("Erro ao buscar estatísticas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Shoplink</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Estilos para os cartões de estatística */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-card {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin-top: 0;
            font-size: 1.2em;
            color: #555;
        }
        .stat-card p {
            font-size: 2.5em;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0 0 0;
        }
        .stat-card a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header class="main-header" style="padding: 15px; margin-bottom: 0;">
        <h1>Painel de Administração</h1>
        <nav>
            <a href="index.php" style="color: white; margin-right: 15px;  font-weight: bold;">Dashboard</a>
            <a href="pedidos.php" style="color: white; margin-right: 15px;">Pedidos</a>
            <a href="produtos.php" style="color: white; margin-right: 15px;">Produtos</a>
            <a href="categorias.php" style="color: white; margin-right: 15px;">Categorias</a>
            <a href="adicionar_produto.php" style="color: white; margin-right: 15px;">Adicionar Produto</a>
            <a href="../logout.php" style="color: #ffcccc; margin-right: 15px;">Sair</a>
        </nav>
    </header>

    <main class="container">
        <h2>Dashboard</h2>
        <p>Olá, <?php echo htmlspecialchars($nome_loja_logado); ?>! Bem-vindo ao seu painel.</p>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Pedidos Pendentes</h3>
                <p><?php echo $pedidos_pendentes; ?></p>
                <a href="pedidos.php">Ver Pedidos &rarr;</a>
            </div>
            <div class="stat-card">
                <h3>Produtos Cadastrados</h3>
                <p><?php echo $total_produtos; ?></p>
                <a href="produtos.php">Gerir Produtos &rarr;</a>
            </div>
            <div class="stat-card">
                <h3>Categorias Criadas</h3>
                <p><?php echo $total_categorias; ?></p>
                <a href="categorias.php">Gerir Categorias &rarr;</a>
            </div>
        </div>
    </main>
</body>
</html>