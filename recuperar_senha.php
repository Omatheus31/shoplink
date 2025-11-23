<?php
require_once 'config/database.php';
require_once 'includes/email.php'; // <--- Inclui nosso carteiro
$titulo_pagina = "Recuperar Senha";
require_once 'includes/header_public.php';

$mensagem = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // 1. Verifica se o email existe
    $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Gera o Token e o Link (Isso deve ser um link LOCAL ou NGROK se estiver apresentando)
        // Dica: Para o vídeo, use localhost. Se usar ngrok, lembre de atualizar aqui.
        $token = base64_encode($email);
        $link = "http://localhost/shoplink/redefinir_senha.php?token=" . $token;

        // 3. Monta o HTML do E-mail
        $corpo_email = "
        <h2>Recuperação de Senha</h2>
        <p>Olá, <strong>{$user['nome']}</strong>.</p>
        <p>Recebemos uma solicitação para redefinir sua senha no Shoplink.</p>
        <p>Clique no link abaixo para criar uma nova senha:</p>
        <p><a href='$link' style='background:#0d6efd; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Redefinir Minha Senha</a></p>
        <p>Se não foi você, ignore este e-mail.</p>
        ";

        // 4. Tenta Enviar
        if (enviarEmail($email, $user['nome'], 'Redefinir Senha - Shoplink', $corpo_email)) {
            $mensagem = "E-mail enviado com sucesso para <strong>$email</strong>! Verifique sua caixa de entrada.";
            $tipo_alerta = "success";
        } else {
            $mensagem = "Erro ao enviar e-mail. Verifique sua conexão ou tente mais tarde.";
            $tipo_alerta = "danger";
        }

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
                <?php endif; ?>

                <?php if (empty($mensagem) || $tipo_alerta === 'danger'): ?>
                    <p class="text-muted mb-4 small">Digite seu e-mail para receber o link de redefinição.</p>
                    <form method="post">
                        <div class="form-floating mb-3 text-start">
                            <input type="email" class="form-control" id="email" name="email" placeholder="email" required>
                            <label for="email">E-mail Cadastrado</label>
                        </div>
                        <button class="btn btn-primary w-100 py-2" onclick="this.innerHTML='Enviando...'">Enviar Link</button>
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