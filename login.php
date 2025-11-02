<?php
// Inicia a sessão no topo de CADA página que vai usar sessões
session_start();

// Se o usuário já estiver logado, redireciona para o painel de admin
if (isset($_SESSION['id_usuario'])) {
    header("Location: admin/pedidos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shoplink Admin</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h1 {
            margin-top: 0;
            color: #2c3e50;
        }
        .login-form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .login-form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .login-form-group input {
            width: 95%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .login-btn {
            width: 100%;
            padding: 0.85rem;
            border: none;
            border-radius: 4px;
            background-color: #3498db;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-btn:hover {
            background-color: #2980b9;
        }
        .login-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Shoplink</h1>
        <p>Acesse seu painel de administrador</p>

        <?php 
        // Verifica se há uma mensagem de erro vinda da URL
        if (isset($_GET['erro'])) {
            $erro = '';
            switch ($_GET['erro']) {
                case 'credenciais':
                    $erro = 'E-mail ou senha incorretos.';
                    break;
                case 'acesso_negado':
                    $erro = 'Você precisa fazer login para acessar esta página.';
                    break;
            }
            if ($erro) {
                echo '<div class="login-error">' . $erro . '</div>';
            }
        }
        ?>

        <form action="processa_login.php" method="POST">
            <?php
            if (isset($_GET['redirect_url'])) {
                echo '<input type="hidden" name="redirect_url" value="' . htmlspecialchars($_GET['redirect_url']) . '">';
            }
            ?>
            <div class="login-form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="login-form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="login-btn">Entrar</button>
        </form>
    </div>
</body>
</html>