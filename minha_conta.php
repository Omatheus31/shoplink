<?php
// minha_conta.php
require_once 'config/database.php';
$titulo_pagina = 'Meus Dados';
require_once 'includes/header_public.php';

// 1. VERIFICA LOGIN
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php?erro=acesso_negado');
    exit();
}

$id = $_SESSION['id_usuario'];
$mensagem_sucesso = '';
$mensagem_erro = '';

// 2. PROCESSA ATUALIZAÇÃO DE PERFIL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_perfil') {
    try {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';

        if (empty($nome) || empty($email)) {
            throw new Exception('Nome e email são obrigatórios.');
        }

        // Busca a senha atual (hash) no banco para validar
        $stmt_check = $pdo->prepare("SELECT senha_hash FROM usuarios WHERE id = :id");
        $stmt_check->execute([':id' => $id]);
        $usuario_atual = $stmt_check->fetch();

        // --- LÓGICA DE ATUALIZAÇÃO ---
        $sql = "UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, 
                endereco_rua = :rua, endereco_numero = :numero, endereco_bairro = :bairro, 
                endereco_cidade = :cidade, endereco_estado = :estado";
        
        $params = [
            ':nome' => $nome, ':email' => $email, ':telefone' => $_POST['telefone'],
            ':rua' => $_POST['endereco_rua'], ':numero' => $_POST['endereco_numero'],
            ':bairro' => $_POST['endereco_bairro'], ':cidade' => $_POST['endereco_cidade'],
            ':estado' => $_POST['endereco_estado'], ':id' => $id
        ];

        // Se o usuário tentou digitar uma nova senha
        if (!empty($nova_senha)) {
            // 1. Verifica se digitou a senha atual
            if (empty($senha_atual)) {
                throw new Exception("Para alterar a senha, você deve informar sua senha atual.");
            }
            // 2. Verifica se a senha atual está correta
            if (!password_verify($senha_atual, $usuario_atual['senha_hash'])) {
                throw new Exception("A senha atual informada está incorreta.");
            }
            // 3. Verifica tamanho mínimo
            if (strlen($nova_senha) < 6) {
                throw new Exception("A nova senha deve ter no mínimo 6 caracteres.");
            }

            // Adiciona a nova senha na query
            $sql .= ", senha_hash = :nova_senha";
            $params[':nova_senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Atualiza nome na sessão
        $_SESSION['nome'] = $nome;
        $mensagem_sucesso = "Dados atualizados com sucesso!";

    } catch (Exception $e) {
        $mensagem_erro = $e->getMessage();
    }
}

// 3. BUSCA DADOS DO USUÁRIO PARA PREENCHER O FORMULÁRIO
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id');
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch();
?>

<?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
    <div class="alert alert-success text-center shadow-sm mb-4">
        <h3><i class="bi bi-check-circle-fill"></i> Pedido Realizado!</h3>
        <p>Obrigado pela compra. Acompanhe em <a href="meus_pedidos.php" class="alert-link">Meus Pedidos</a>.</p>
    </div>
<?php endif; ?>

<?php if ($mensagem_sucesso): ?> <div class="alert alert-success"><?php echo $mensagem_sucesso; ?></div> <?php endif; ?>
<?php if ($mensagem_erro): ?> <div class="alert alert-danger"><?php echo $mensagem_erro; ?></div> <?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h4 class="h5 mb-0"><i class="bi bi-person-circle"></i> Editar Meus Dados</h4>
            </div>
            <div class="card-body p-4">
                <form method="post" action="minha_conta.php">
                    <input type="hidden" name="acao" value="atualizar_perfil">
                    
                    <h6 class="text-muted small text-uppercase mb-3">Informações Pessoais</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Telefone / WhatsApp</label>
                            <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="text-muted small text-uppercase mb-3">Endereço de Entrega Padrão</h6>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-9">
                            <input type="text" name="endereco_rua" class="form-control" placeholder="Rua/Av" value="<?php echo htmlspecialchars($usuario['endereco_rua'] ?? ''); ?>">
                        </div>
                        <div class="col-3">
                            <input type="text" name="endereco_numero" class="form-control" placeholder="Nº" value="<?php echo htmlspecialchars($usuario['endereco_numero'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-12">
                            <input type="text" name="endereco_bairro" class="form-control mb-2" placeholder="Bairro" value="<?php echo htmlspecialchars($usuario['endereco_bairro'] ?? ''); ?>">
                        </div>
                        <div class="col-8">
                            <input type="text" name="endereco_cidade" class="form-control" placeholder="Cidade" value="<?php echo htmlspecialchars($usuario['endereco_cidade'] ?? ''); ?>">
                        </div>
                        <div class="col-4">
                            <input type="text" name="endereco_estado" class="form-control" placeholder="UF" value="<?php echo htmlspecialchars($usuario['endereco_estado'] ?? ''); ?>">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="text-muted small text-uppercase mb-3 text-danger">Alterar Senha</h6>
                    <div class="alert alert-light border text-muted small">
                        <i class="bi bi-info-circle"></i> Preencha abaixo <strong>apenas</strong> se desejar trocar sua senha.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Senha Atual (Obrigatório para alterar)</label>
                            <input type="password" name="senha_atual" class="form-control" placeholder="Digite sua senha atual">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nova Senha</label>
                            <input type="password" name="nova_senha" class="form-control" placeholder="Crie uma nova senha">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button class="btn btn-primary btn-lg">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_public.php'; ?>