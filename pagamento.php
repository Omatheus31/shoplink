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
    $sql = "SELECT * FROM pedidos WHERE id = :id AND id_usuario = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_pedido, ':uid' => $id_usuario_logado]);
    $pedido = $stmt->fetch();

    if (!$pedido || $pedido['status'] !== 'Aguardando Pagamento') {
        header("Location: meus_pedidos.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

$metodo = $pedido['metodo_pagamento'];
$valor = number_format($pedido['total_pedido'], 2, ',', '.');

// Gera código PIX Fake mas com dados reais (para parecer sério)
$pix_code = "00020126330014br.gov.bcb.pix0114+55939999999995204000053039865405{$pedido['total_pedido']}5802BR5913SHOPLINK LOJA6008SANTAREM62070503***6304";
// API do QR Code
$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($pix_code);
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
                        <small>Escaneie o QR Code ou copie o código abaixo.</small>
                    </div>
                    <img src="<?php echo $qr_api; ?>" alt="QR Code PIX" class="img-fluid border p-2 rounded mb-3" style="max-width: 200px;">
                    
                    <div class="input-group mb-4">
                        <input type="text" class="form-control form-control-sm" value="<?php echo $pix_code; ?>" readonly id="pix-copy">
                        <button class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('pix-copy').value); alert('Copiado!');"><i class="bi bi-clipboard"></i> Copiar</button>
                    </div>

                    <a href="pedido_detalhe.php?id=<?php echo $id_pedido; ?>&compra_sucesso=true" class="btn btn-success w-100 btn-lg">
                        <i class="bi bi-check-circle-fill"></i> Já realizei o pagamento
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

                    <a href="pedido_detalhe.php?id=<?php echo $id_pedido; ?>&compra_sucesso=true" class="btn btn-success w-100 btn-lg">
                        <i class="bi bi-check-circle-fill"></i> Finalizar
                    </a>

                <?php else: ?>
                    <div class="alert alert-info bg-opacity-10 border-info text-start mb-4">
                        <div class="d-flex">
                            <div class="me-2"><i class="bi bi-shield-lock text-info"></i></div>
                            <div class="small lh-sm">Ambiente Seguro. Preencha os dados do cartão.</div>
                        </div>
                    </div>
                    
                    <form action="pedido_detalhe.php" method="GET">
                        <input type="hidden" name="id" value="<?php echo $id_pedido; ?>">
                        <input type="hidden" name="compra_sucesso" value="true">

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
                                <input type="text" class="form-control" placeholder="123" maxlength="3" required>
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