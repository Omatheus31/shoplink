<?php
// redefinir_senha.php
require_once 'config/database.php';
$titulo_pagina = "Nova Senha";
require_once 'includes/header_public.php';

$email_recuperacao = "";
$erro = "";
$sucesso = false;

// 1. Verifica o Token na URL (GET)
if (isset($_GET['token'])) {
    $email_recuperacao = base64_decode($_GET['token']);
}

// 2. Processa o Formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_post = $_POST['email'];
    $senha = $_POST['senha'];
    $confirma = $_POST['confirma_senha'];

    // Mantém o email para não perder se der erro
    $email_recuperacao = $email_post; 

    if ($senha !== $confirma) {
        $erro = "As senhas não conferem.";
    } elseif (strlen($senha) < 8) { // Ajustado para 8 caracteres (Padrão do sistema)
        $erro = "A senha deve ter no mínimo 8 caracteres.";
    } else {
        try {
            // Criptografa e Atualiza
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE usuarios SET senha_hash = :senha WHERE email = :email");
            $stmt->execute([':senha' => $senha_hash, ':email' => $email_post]);
            
            if ($stmt->rowCount() > 0) {
                $sucesso = true;
            } else {
                // Se não afetou nenhuma linha, talvez o email não exista ou a senha já era essa
                $erro = "Erro ao atualizar. Verifique se o email está correto.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                
                <?php if ($sucesso): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle-fill text-success display-1"></i>
                        <h3 class="mt-3 fw-bold">Senha Alterada!</h3>
                        <p class="text-muted mb-4">Sua senha foi redefinida com sucesso.</p>
                        <a href="login.php" class="btn btn-success w-100 btn-lg">Fazer Login Agora</a>
                    </div>
                <?php elseif (empty($email_recuperacao)): ?>
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle-fill"></i> Link inválido ou expirado. 
                        <br><a href="recuperar_senha.php" class="alert-link">Solicitar novo link</a>.
                    </div>
                <?php else: ?>
                    
                    <h3 class="text-center mb-2 fw-bold">Criar Nova Senha</h3>
                    <p class="text-center text-muted small mb-4">
                        Definindo senha para: <strong><?php echo htmlspecialchars($email_recuperacao); ?></strong>
                    </p>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger text-center py-2"><?php echo $erro; ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_recuperacao); ?>">
                        
                        <div class="form-floating mb-3">
                            <!-- CORREÇÃO: placeholder vazio para não sobrepor o label -->
                            <input type="password" class="form-control" id="senha" name="senha" placeholder=" " required minlength="8">
                            <label for="senha">Nova Senha (Mín. 8 caracteres)</label>
                        </div>
                        
                        <div class="form-floating mb-4">
                            <!-- CORREÇÃO: placeholder vazio para não sobrepor o label -->
                            <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" placeholder=" " required>
                            <label for="confirma_senha">Confirme a Nova Senha</label>
                        </div>

                        <button class="btn btn-primary w-100 py-2 fw-bold">Salvar Nova Senha</button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_public.php'; ?>