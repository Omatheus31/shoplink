<?php
// admin/configuracoes.php
$titulo_pagina = "Configurações";
require_once 'includes/header_admin.php';

// Salvar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_nome = trim($_POST['nome_loja']);
    $pdo->prepare("UPDATE configuracoes SET valor = ? WHERE chave = 'nome_loja'")->execute([$novo_nome]);
    $sucesso = "Nome da loja atualizado!";
}

// Buscar Atual
$stmt = $pdo->query("SELECT valor FROM configuracoes WHERE chave = 'nome_loja'");
$nome_atual = $stmt->fetchColumn() ?: 'Shoplink';
?>

<h2 class="h2">Configurações da Loja</h2>
<?php if(isset($sucesso)) echo "<div class='alert alert-success'>$sucesso</div>"; ?>

<div class="card shadow-sm mt-3">
    <div class="card-body">
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Nome da Loja (Aparece no topo do site)</label>
                <input type="text" name="nome_loja" class="form-control" value="<?php echo htmlspecialchars($nome_atual); ?>">
            </div>
            <button class="btn btn-primary">Salvar Configuração</button>
        </form>
    </div>
</div>
<?php require_once 'includes/footer_admin.php'; ?>