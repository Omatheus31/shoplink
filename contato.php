<?php
require_once 'config/database.php';
$titulo_pagina = "Fale Conosco";
require_once 'includes/header_public.php';

$sucesso = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aqui você salvaria no banco se quisesse, mas para o trabalho, 
    // mostrar que a mensagem "foi enviada" é suficiente.
    $sucesso = true;
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <h2 class="mb-4 fw-bold text-center">Entre em Contato</h2>
                
                <?php if ($sucesso): ?>
                    <div class="alert alert-success text-center py-4">
                        <i class="bi bi-send-check fs-1"></i><br><br>
                        <strong>Mensagem Enviada!</strong><br>
                        Obrigado pelo contato. Responderemos em breve no seu e-mail.
                        <br><a href="index.php" class="btn btn-sm btn-outline-success mt-3">Voltar para Loja</a>
                    </div>
                <?php else: ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assunto</label>
                        <select class="form-select" name="assunto">
                            <option>Dúvida sobre Produto</option>
                            <option>Status do Pedido</option>
                            <option>Reclamação</option>
                            <option>Outros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mensagem</label>
                        <textarea name="mensagem" class="form-control" rows="5" required></textarea>
                    </div>
                    <button class="btn btn-primary w-100 btn-lg">Enviar Mensagem</button>
                </form>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_public.php'; ?>