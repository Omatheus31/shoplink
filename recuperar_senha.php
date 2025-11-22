<?php
// recuperar_senha.php
require_once 'config/database.php';
$titulo_pagina = "Recuperar Senha";
require_once 'includes/header_public.php';

$mensagem = "";
$tipo_alerta = "";
$link_simulado = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // 1. Verifica se o email existe
    $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Gera um token simples (base64 do email) para simulação
        // Em produção real, seria um hash único salvo no banco com validade de tempo.
        $token = base64_encode($email);
        
        $mensagem = "Um link de recuperação foi enviado para <strong>" . htmlspecialchars($email) . "</strong>.";
        $tipo_alerta = "success";
        
        // O LINK MÁGICO (Simulando o e-mail)
        $link_simulado = "redefinir_senha.php?token=" . $token;
    } else {
        $mensagem = "E-mail não encontrado em nossa base de dados.";
        $tipo_alerta = "danger";
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-body p-4 text-center">
                <i class="bi bi-envelope-paper-heart text-primary display-1 mb-3"></i>
                <h3 class="mb-2">Recuperação de Conta</h3>
                
                <?php if ($mensagem): ?>
                    <div class="alert alert-<?php echo $tipo_alerta; ?> text-start">
                        <?php echo $mensagem; ?>
                    </div>
                    
                    <?php if ($tipo_alerta === 'success'): ?>
                        <div class="card bg-light border-warning mb-3 text-start">
                            <div class="card-header bg-warning text-dark small fw-bold">
                                <i class="bi bi-bug-fill"></i> MODO DEBUG (Simulação de E-mail)
                            </div>
                            <div class="card-body small">
                                <p class="mb-1">Assunto: Redefinir Senha - Shoplink</p>
                                <p class="mb-2">Olá, <?php echo htmlspecialchars($user['nome']); ?>. Clique abaixo para trocar sua senha:</p>
                                <a href="<?php echo $link_simulado; ?>" class="btn btn-sm btn-primary">Redefinir Minha Senha</a>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

                <?php if (empty($mensagem) || $tipo_alerta === 'danger'): ?>
                    <p class="text-muted mb-4 small">Digite seu e-mail para receber o link de redefinição.</p>
                    <form method="post">
                        <div class="form-floating mb-3 text-start">
                            <input type="email" class="form-control" id="email" name="email" placeholder="email" required>
                            <label for="email">E-mail Cadastrado</label>
                        </div>
                        <button class="btn btn-primary w-100 py-2">Enviar Link</button>
                    </form>
                <?php endif; ?>
                
                <div class="mt-3">
                    <a href="login.php" class="text-decoration-none small">Voltar para Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_public.php'; ?>