<?php
// Inicia a sessão
session_start();

// Se o usuário já estiver logado, redireciona para o painel de admin
if (isset($_SESSION['id_usuario'])) {
    header("Location: admin/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crie sua Loja - Shoplink</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }
        .login-container {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .login-container h1 {
            margin-top: 0;
            color: #2c3e50;
        }
        .login-form-group {
            margin-bottom: 1.2rem;
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
            background-color: #27ae60; /* Cor verde para cadastro */
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-btn:hover {
            background-color: #229954;
        }
        .login-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .login-footer {
            margin-top: 1.5rem;
            color: #555;
        }
        .login-footer a {
            color: #3498db;
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Crie sua Conta na Shoplink</h1>
        <p>Comece a vender hoje mesmo.</p>

        <?php 
        // Verifica se há uma mensagem de erro vinda da URL
        if (isset($_GET['erro'])) {
            $erro = '';
            if ($_GET['erro'] === 'email_existe') {
                $erro = 'Este e-mail já está cadastrado. Tente outro.';
            } elseif ($_GET['erro'] === 'senhas_nao_conferem') {
                $erro = 'As senhas não conferem.';
            } else {
                $erro = 'Ocorreu um erro. Tente novamente.';
            }
            echo '<div class="login-error">' . $erro . '</div>';
        }
        ?>

        <form action="processa_cadastro.php" method="POST">
            <div class="login-form-group">
                <label for="nome_loja">Nome da sua Loja:</label>
                <input type="text" id="nome_loja" name="nome_loja" required>
            </div>
            <div class="login-form-group">
                <label for="email">Seu E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="login-form-group">
                <label for="senha">Crie uma Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div class="login-form-group">
                <label for="confirma_senha">Confirme sua Senha:</label>
                <input type="password" id="confirma_senha" name="confirma_senha" required>
            </div>
            <button type="submit" class="login-btn">Criar minha loja</button>
        </form>

        <div class="login-footer">
            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
        </div>
    </div>
</body>
</html>