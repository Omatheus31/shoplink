<?php
// redefinir_senha.php
require_once 'config/database.php';
$titulo_pagina = "Nova Senha";
require_once 'includes/header_public.php';

$email_recuperacao = "";
$erro = "";
$sucesso = false;

// 1. Verifica o Token na URL
if (isset($_GET['token'])) {
    // Decodifica o email (na simulação usamos base64)
    $email_recuperacao = base64_decode($_GET['token']);
}

// 2. Processa o Formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_post = $_POST['email'];
    $senha = $_POST['senha'];
    $confirma = $_POST['confirma_senha'];

    if ($senha !== $confirma) {
        $erro = "As senhas não conferem.";
        $email_recuperacao = $email_post; // Mantém o email para não perder
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter no mínimo 6 caracteres.";
        $email_recuperacao = $email_post;
    } else {
        // TUDO CERTO: ATUALIZA NO BANCO
        try {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET senha_hash = :senha WHERE email = :email");
            $stmt->execute([':senha' => $senha_hash, ':email' => $email_post]);
            $sucesso = true;
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                
                <?php if ($sucesso): ?>
                    <div class="text-center">
                        <i class="bi bi-check-circle-fill text-success display-1"></i>
                        <h3 class="mt-3">Senha Alterada!</h3>
                        <p class="text-muted">Sua senha foi redefinida com sucesso.</p>
                        <a href="login.php" class="btn btn-success w-100">Fazer Login Agora</a>
                    </div>
                <?php elseif (empty($email_recuperacao)): ?>
                    <div class="alert alert-danger text-center">
                        Link inválido ou expirado. <a href="recuperar_senha.php">Tente novamente</a>.
                    </div>
                <?php else: ?>
                    
                    <h3 class="text-center mb-3">Criar Nova Senha</h3>
                    <p class="text-center text-muted small mb-4">Defina uma nova senha para <strong><?php echo htmlspecialchars($email_recuperacao); ?></strong></p>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo $erro; ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_recuperacao); ?>">
                        
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Nova Senha" required minlength="6">
                            <label for="senha">Nova Senha</label>
                        </div>
                        
                        <div class="form-floating mb-4">
                            <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" placeholder="Confirme" required>
                            <label for="confirma_senha">Confirme a Nova Senha</label>
                        </div>

                        <button class="btn btn-primary w-100 py-2">Salvar Nova Senha</button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_public.php'; ?>