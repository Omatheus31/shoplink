<?php
// pagamento.php
require_once 'config/database.php';
$titulo_pagina = "Pagamento"; 
require_once 'includes/header_public.php';

if (!isset($_GET['id_pedido']) || !isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

$id_pedido = (int)$_GET['id_pedido'];
$id_usuario_logado = $_SESSION['id_usuario'];

try {
    // Busca o pedido E o método de pagamento
    $sql = "SELECT * FROM pedidos WHERE id = :id AND id_usuario = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_pedido, ':uid' => $id_usuario_logado]);
    $pedido = $stmt->fetch();

    if (!$pedido || $pedido['status'] !== 'Aguardando Pagamento') {
        header("Location: minha_conta.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

// Dados para simulação
$metodo = $pedido['metodo_pagamento'];
$valor = number_format($pedido['total_pedido'], 2, ',', '.');
?>

<div class="row justify-content-center mt-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 text-center">
            
            <div class="card-header bg-white py-4 border-bottom-0">
                <div class="mb-3">
                    <?php if ($metodo === 'PIX'): ?>
                        <i class="bi bi-qr-code text-success display-1"></i>
                    <?php elseif ($metodo === 'Boleto'): ?>
                        <i class="bi bi-upc-scan text-dark display-1"></i>
                    <?php else: ?>
                        <i class="bi bi-credit-card text-primary display-1"></i>
                    <?php endif; ?>
                </div>
                <h2 class="h4 fw-bold">Pagamento via <?php echo htmlspecialchars($metodo); ?></h2>
                <p class="text-muted">Valor Total: <span class="fw-bold text-dark">R$ <?php echo $valor; ?></span></p>
            </div>

            <div class="card-body px-4 pb-5">
                
                <?php if ($metodo === 'PIX'): ?>
                    <div class="alert alert-success bg-opacity-10 border-success">
                        <small>Use o App do seu banco para pagar.</small>
                    </div>
                    <img src="assets/img/qr-code.jpg" alt="QR Code" class="img-fluid border p-2 rounded mb-3" style="max-width: 200px;">
                    
                    <div class="input-group mb-4">
                        <input type="text" class="form-control form-control-sm" value="00020126330014br.gov.bcb.pix..." readonly>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-clipboard"></i> Copiar</button>
                    </div>

                    <a href="minha_conta.php?status=sucesso" class="btn btn-success w-100 btn-lg">
                        <i class="bi bi-check-circle-fill"></i> Confirmar Pagamento
                    </a>
                
                <?php elseif ($metodo === 'Boleto'): ?>
                    <div class="alert alert-warning bg-opacity-10 border-warning">
                        <small>Vencimento em 3 dias úteis.</small>
                    </div>
                    <div class="p-3 bg-light border rounded mb-4 font-monospace text-break">
                        34191.79001 01043.510047 91020.150008 5 839500000<?php echo str_replace([',','.'], '', $valor); ?>
                    </div>
                    <button class="btn btn-outline-dark w-100 mb-3" onclick="window.print()">
                        <i class="bi bi-printer"></i> Imprimir Boleto
                    </button>

                    <a href="minha_conta.php?status=sucesso" class="btn btn-success w-100 btn-lg">
                        <i class="bi bi-check-circle-fill"></i> Confirmar Pagamento
                    </a>

                <?php else: ?>
                    <div class="alert alert-info bg-opacity-10 border-info text-start mb-4">
                        <div class="d-flex">
                            <div class="me-2"><i class="bi bi-shield-lock text-info"></i></div>
                            <div class="small lh-sm">Ambiente Seguro. Preencha os dados do cartão para processar o pagamento.</div>
                        </div>
                    </div>
                    
                    <form action="minha_conta.php" method="GET">
                        <input type="hidden" name="status" value="sucesso">

                        <div class="mb-3 text-start">
                            <label class="form-label small fw-bold text-muted">Número do Cartão</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-credit-card"></i></span>
                                <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-6 mb-3 text-start">
                                <label class="form-label small fw-bold text-muted">Validade</label>
                                <input type="text" class="form-control" placeholder="MM/AA" maxlength="5" required>
                            </div>
                            <div class="col-6 mb-3 text-start">
                                <label class="form-label small fw-bold text-muted">CVV</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="123" maxlength="3" required>
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-question-circle"></i></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4 text-start">
                            <label class="form-label small fw-bold text-muted">Nome no Cartão</label>
                            <input type="text" class="form-control" placeholder="COMO ESTÁ NO CARTAO" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="bi bi-lock-fill"></i> Pagar R$ <?php echo $valor; ?>
                        </button>
                    </form>
                <?php endif; ?>

                <p class="mt-3 mb-0"><small class="text-muted">Simulação Acadêmica - Nenhum valor será cobrado.</small></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer_public.php'; ?>