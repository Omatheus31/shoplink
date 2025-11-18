<?php
// 1. O GUARDIÃO! (O header_public.php vai iniciar a sessão)
require_once 'config/database.php';

// 2. INCLUI O NOVO CABEÇALHO BOOTSTRAP
$titulo_pagina = "Minha Conta"; 
require_once 'includes/header_public.php';

// 3. Garante que o utilizador está logado
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['redirect_url_apos_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php?erro=acesso_negado");
    exit();
}

$id_usuario_logado = $_SESSION['id_usuario'];

try {
    // 4. BUSCA OS DADOS DO UTILIZADOR (para o perfil)
    $stmt_usuario = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt_usuario->execute([':id' => $id_usuario_logado]);
    $usuario = $stmt_usuario->fetch();

    if (!$usuario) {
        session_destroy();
        header("Location: login.php?erro=usuario_invalido");
        exit();
    }

    // 5. BUSCA O HISTÓRICO DE PEDIDOS DO UTILIZADOR
    $sql_pedidos = "SELECT * FROM pedidos WHERE id_usuario = :id_usuario ORDER BY id DESC";
    $stmt_pedidos = $pdo->prepare($sql_pedidos);
    $stmt_pedidos->execute([':id_usuario' => $id_usuario_logado]);
    $pedidos = $stmt_pedidos->fetchAll();

} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}

// Formata o endereço salvo para exibição
$endereco_salvo = htmlspecialchars($usuario['endereco_rua']) . ', ' . htmlspecialchars($usuario['endereco_numero']) . ' - ' . htmlspecialchars($usuario['endereco_bairro']) . ', ' . htmlspecialchars($usuario['endereco_cidade']) . ' - ' . htmlspecialchars($usuario['endereco_estado']);
?>

<!-- =============================================== -->
<!-- INÍCIO DO CONTEÚDO DA PÁGINA (Refatorado) -->
<!-- =============================================== -->

<!-- Mensagem de Sucesso do Pagamento (com classes Bootstrap) -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'pagamento_processando'): ?>
    <div class="alert alert-success text-center shadow-sm">
        <h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Obrigado!</h4>
        <p>Seu pagamento está sendo processado. A loja foi notificada e entrará em contato em breve.</p>
    </div>
<?php endif; ?>

<h2 class="mb-4">Minha Conta</h2>

<div class="row g-4">

    <!-- Coluna da Esquerda (Perfil) -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h3 class="h5 mb-0"><i class="bi bi-person-badge"></i> Meus Dados</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Nome:</strong><br>
                        <?php echo htmlspecialchars($usuario['nome_loja']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>E-mail:</strong><br>
                        <?php echo htmlspecialchars($usuario['email']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Telefone:</strong><br>
                        <?php echo htmlspecialchars($usuario['telefone']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Endereço Principal:</strong><br>
                        <?php echo $endereco_salvo; ?>
                    </li>
                </ul>
                <a href="#" class="btn btn-outline-primary btn-sm mt-3">Editar Perfil</a>
                <a href="#" class="btn btn-outline-danger btn-sm mt-3">Excluir Conta</a>
            </div>
        </div>
    </div>

    <!-- Coluna da Direita (Histórico de Pedidos) -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h3 class="h5 mb-0"><i class="bi bi-receipt"></i> Meus Pedidos</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Nº Pedido</th>
                                <th scope="col">Data</th>
                                <th scope="col">Valor Total</th>
                                <th scope="col">Status</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($pedidos): ?>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td class_="fw-bold">#<?php echo $pedido['id']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                        <td>R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?></td>
                                        <td>
                                            <!-- Lógica de Cor do Status -->
                                            <?php
                                            $status = $pedido['status'];
                                            $classe_status = 'status-aguardando'; // Padrão
                                            if ($status == 'Concluído' || $status == 'Pago') $classe_status = 'status-concluido';
                                            if ($status == 'Enviado') $classe_status = 'status-enviado';
                                            if ($status == 'Cancelado') $classe_status = 'status-cancelado';
                                            if ($status == 'Em Separação') $classe_status = 'status-separacao';
                                            ?>
                                            <span class="<?php echo $classe_status; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-secondary">Ver Detalhes</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Você ainda não fez nenhum pedido.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div> <!-- fim .table-responsive -->
            </div>
        </div>
    </div>
</div>

<!-- =============================================== -->
<!-- FIM DO CONTEÚDO DA PÁGINA -->
<!-- =============================================== -->

<?php
// 6. INCLUI O NOVO RODAPÉ BOOTSTRAP
require_once 'includes/footer_public.php';
?>