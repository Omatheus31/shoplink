<?php
// admin/perfil.php
$titulo_pagina = "Meu Perfil";
require_once 'includes/header_admin.php';

$id_usuario = $_SESSION['id_usuario'];
$mensagem = '';
$tipo_alerta = '';

// PROCESSAMENTO DO FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirma_senha = $_POST['confirma_senha'] ?? '';

        if (empty($nome) || empty($email)) {
            throw new Exception("Nome e E-mail são obrigatórios.");
        }

        // Busca dados atuais (para validar senha)
        $stmt = $pdo->prepare("SELECT senha_hash FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id_usuario]);
        $admin_atual = $stmt->fetch();

        // Query base
        $sql = "UPDATE usuarios SET nome = :nome, email = :email";
        $params = [':nome' => $nome, ':email' => $email, ':id' => $id_usuario];

        // Se for trocar a senha
        if (!empty($nova_senha)) {
            if (empty($senha_atual)) {
                throw new Exception("Para mudar a senha, informe a senha atual.");
            }
            if (!password_verify($senha_atual, $admin_atual['senha_hash'])) {
                throw new Exception("Senha atual incorreta.");
            }
            if (strlen($nova_senha) < 6) {
                throw new Exception("A nova senha deve ter no mínimo 6 caracteres.");
            }
            if ($nova_senha !== $confirma_senha) {
                throw new Exception("A nova senha e a confirmação não conferem.");
            }

            $sql .= ", senha_hash = :senha";
            $params[':senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        $stmt_up = $pdo->prepare($sql);
        $stmt_up->execute($params);

        // Atualiza sessão e feedback
        $_SESSION['nome'] = $nome;
        $mensagem = "Perfil atualizado com sucesso!";
        $tipo_alerta = "success";

    } catch (Exception $e) {
        $mensagem = $e->getMessage();
        $tipo_alerta = "danger";
    }
}

// BUSCAR DADOS PARA EXIBIR
$stmt = $pdo->prepare("SELECT nome, email FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id_usuario]);
$dados = $stmt->fetch();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Meu Perfil Admin</h1>
</div>

<?php if ($mensagem): ?>
    <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
        <?php echo $mensagem; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="post">
                    <h5 class="text-muted mb-3">Dados Pessoais</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($dados['nome']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($dados['email']); ?>" required>
                    </div>

                    <hr class="my-4">
                    
                    <h5 class="text-muted mb-3 text-danger">Alterar Senha</h5>
                    <p class="small text-muted">Preencha apenas se desejar mudar sua senha de acesso.</p>

                    <div class="mb-3">
                        <label class="form-label">Senha Atual</label>
                        <input type="password" name="senha_atual" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nova Senha</label>
                            <input type="password" name="nova_senha" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirmar Nova Senha</label>
                            <input type="password" name="confirma_senha" class="form-control">
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_admin.php'; ?>