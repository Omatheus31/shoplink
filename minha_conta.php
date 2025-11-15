<?php
// 1. O GUARDIÃO! (Verifica se o cliente está logado)
session_start();
if (!isset($_SESSION['id_usuario'])) {
    // Se não estiver logado, chuta para o login
    $_SESSION['redirect_url_apos_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php?erro=acesso_negado");
    exit();
}

// 2. BUSCA TODOS OS DADOS DO UTILIZADOR E SEUS PEDIDOS
require_once 'config/database.php';
$id_usuario_logado = $_SESSION['id_usuario'];

try {
    // 3. BUSCA OS DADOS DO UTILIZADOR (para o perfil)
    $stmt_usuario = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt_usuario->execute([':id' => $id_usuario_logado]);
    $usuario = $stmt_usuario->fetch();

    if (!$usuario) {
        session_destroy();
        header("Location: login.php?erro=usuario_invalido");
        exit();
    }

    // 4. BUSCA O HISTÓRICO DE PEDIDOS DO UTILIZADOR
    $sql_pedidos = "SELECT * FROM pedidos WHERE id_usuario = :id_usuario ORDER BY id DESC";
    $stmt_pedidos = $pdo->prepare($sql_pedidos);
    $stmt_pedidos->execute([':id_usuario' => $id_usuario_logado]);
    $pedidos = $stmt_pedidos->fetchAll();

} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}

// Formata o endereço salvo para exibição
$endereco_salvo = htmlspecialchars($usuario['endereco_rua']) . ', ' . htmlspecialchars($usuario['endereco_numero']) . ' - ' . htmlspecialchars($usuario['endereco_bairro']) . ', ' . htmlspecialchars($usuario['endereco_cidade']) . ' - ' . htmlspecialchars($usuario['endereco_estado']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - Shoplink</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Estilos da tabela (copiados do admin para consistência) */
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .admin-table th { background-color: #f2f2f2; }
        .admin-table tr:nth-child(even) { background-color: #f9f9f9; }
        
        /* Estilos dos Status (copiados do admin) */
        .status-pendente { background-color: #f39c12; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; }
        .status-concluido { background-color: #27ae60; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; }
        .status-pago { background-color: #3498db; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; }
        .status-enviado { background-color: #9b59b6; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; }
        .status-cancelado { background-color: #e74c3c; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; }
        .status-separacao { background-color: #34495e; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; }

        .profile-box {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <h1>Catálogo da sua loja</h1>
        <!-- Menu Dinâmico (copiado do index.php) -->
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="carrinho.php" class="cart-link">
                🛒 Carrinho (<span id="cart-counter">0</span>)
            </a>
            
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <a href="minha_conta.php" class="cart-link" style="background-color: #3498db; border-color: #3498db; color: white;">Minha Conta</a>
                <a href="logout.php" class="cart-link" style="background-color: #e74c3c;">Sair</a>
            <?php else: ?>
                <a href="login.php" class="cart-link" style="background-color: #3498db;">Login / Cadastrar</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="container">
        
        <!-- 5. MENSAGEM DE SUCESSO DO PAGAMENTO -->
        <?php if (isset($_GET['status']) && $_GET['status'] == 'pagamento_processando'): ?>
            <div style="padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724; background-color: #d4edda; text-align: center;">
                Obrigado! Seu pagamento está sendo processado. A loja foi notificada e entrará em contato.
            </div>
        <?php endif; ?>

        <h2>Minha Conta</h2>
        
        <!-- 6. BOX DE DADOS DO PERFIL -->
        <div class="profile-box">
            <h3>Meus Dados</h3>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($usuario['nome_loja']); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($usuario['telefone']); ?></p>
            <p><strong>Endereço Principal:</strong> <?php echo $endereco_salvo; ?></p>
            <!-- (Aqui entrará o botão "Editar Perfil" e "Excluir Conta") -->
        </div>

        <!-- 7. HISTÓRICO DE PEDIDOS -->
        <h3>Meus Pedidos</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nº Pedido</th>
                    <th>Data</th>
                    <th>Valor Total</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pedidos): ?>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?php echo $pedido['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                            <td>R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?></td>
                            <td>
                                <!-- Lógica de Cor do Status -->
                                <?php
                                $status = $pedido['status'];
                                $classe_status = 'status-pendente'; // Padrão
                                if ($status == 'Concluído' || $status == 'Pago') $classe_status = 'status-concluido';
                                if ($status == 'Enviado') $classe_status = 'status-enviado';
                                if ($status == 'Cancelado') $classe_status = 'status-cancelado';
                                if ($status == 'Em Separação') $classe_status = 'status-separacao';
                                ?>
                                <span class="<?php echo $classe_status; ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td>
                                <!-- (Aqui entrará o link "Ver Detalhes") -->
                                <a href="#">Ver Detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Você ainda não fez nenhum pedido.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
    
    <!-- (O toast e o cart.js não são estritamente necessários aqui, mas o cart.js atualiza o contador do carrinho no header) -->
    <div id="toast-notification"></div>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Shoplink</p>
    </footer>
    <script src="assets/js/cart.js"></script>
</body>
</html>