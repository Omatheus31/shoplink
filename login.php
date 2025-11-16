<?php
// O PHP no topo não muda
session_start();
if (isset($_SESSION['id_usuario'])) {
    header("Location: admin/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-100"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shoplink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Estilo para centralizar o formulário na tela */
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="h-100">
    
    <div class="container form-container">
        <div class="col-lg-5 col-md-8 col-sm-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold">Shoplink</h1>
                        <p class="text-muted">Acesse seu painel</p>
                    </div>

                    <?php 
                    if (isset($_GET['erro'])) {
                        $erro = '';
                        switch ($_GET['erro']) {
                            case 'credenciais':
                                $erro = 'E-mail ou senha incorretos.';
                                break;
                            case 'acesso_negado':
                                $erro = 'Você precisa fazer login para acessar esta página.';
                                break;
                            case 'carrinho_login':
                                $erro = 'Você precisa fazer login para ver seu carrinho.';
                                break;
                        }
                        if ($erro) {
                            echo '<div class="alert alert-danger">' . $erro . '</div>';
                        }
                    }
                    if (isset($_GET['status']) && $_GET['status'] === 'cadastro_sucesso') {
                         echo '<div class="alert alert-success">Cadastro realizado com sucesso! Faça o login.</div>';
                    }
                    ?>

                    <form action="processa_login.php" method="POST">
                        <?php
                        if (isset($_SESSION['redirect_url_apos_login'])) {
                            echo '<input type="hidden" name="redirect_url" value="' . htmlspecialchars($_SESSION['redirect_url_apos_login']) . '">';
                            unset($_SESSION['redirect_url_apos_login']); // Limpa a sessão
                        }
                        ?>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                            <label for="email">E-mail</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha" required>
                            <label for="senha">Senha</label>
                        </div>
                        <button type="submit" class="w-100 btn btn-lg btn-primary">Entrar</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">Não tem uma conta?</p>
                        <a href="cadastro.php" class="fw-bold text-decoration-none">Crie uma agora</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>