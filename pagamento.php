<?php
// 1. O GUARDIÃO! (O header_public.php vai iniciar a sessão)
require_once 'config/database.php';

// 2. INCLUI O NOVO CABEÇALHO BOOTSTRAP
$titulo_pagina = "Pagamento do Pedido"; 
require_once 'includes/header_public.php';

// 3. Verifica se o ID do pedido foi passado
if (!isset($_GET['id_pedido']) || empty($_GET['id_pedido'])) {
    header("Location: index.php"); // Se não tem ID, volta ao catálogo
    exit();
}

// 4. Garante que o utilizador está logado (o header já iniciou a sessão)
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['redirect_url_apos_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php?erro=acesso_negado");
    exit();
}

$id_pedido = (int)$_GET['id_pedido'];
$id_usuario_logado = $_SESSION['id_usuario'];

try {
    // 5. BUSCA O PEDIDO NO BANCO
    // Garante que o pedido pertence ao utilizador logado E está aguardando pagamento
    $sql = "SELECT * FROM pedidos 
            WHERE id = :id_pedido 
            AND id_usuario = :id_usuario 
            AND status = 'Aguardando Pagamento'";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_pedido' => $id_pedido,
        ':id_usuario' => $id_usuario_logado
    ]);
    $pedido = $stmt->fetch();

    // Se o pedido não for encontrado (ou não pertencer ao utilizador, ou já foi pago)
    if (!$pedido) {
        // Redireciona para a página de "Meus Pedidos" (que vamos criar)
        header("Location: minha_conta.php?erro=pedido_invalido");
        exit();
    }

} catch (PDOException $e) {
    die("Erro ao buscar pedido: " . $e->getMessage());
}

// --- DADOS FICTÍCIOS DO PIX (PARA SIMULAÇÃO) ---
// Na vida real, isto seria gerado por uma API de pagamento (ex: Mercado Pago)
$qr_code_imagem_url = "https://i.imgur.com/g88v19G.png"; // Um QR Code PIX de exemplo
$pix_copia_cola = "00020126330014br.gov.bcb.pix0111123456789015204000053039865405200.005802BR5913NOME DA LOJA6009SAO PAULO62070503***6304ABCD";

?>

<!-- =============================================== -->
<!-- INÍCIO DO CONTEÚDO DA PÁGINA (Refatorado) -->
<!-- =============================================== -->

<div class="row justify-content-center">
    <div class="col-lg-6">
        <!-- Card de Pagamento -->
        <div class="card shadow-sm border-0">
            <!-- Cabeçalho do Card -->
            <div class="card-header bg-white text-center p-4">
                <h2 class="h5 mb-1 text-muted">Total a pagar (Pedido #<?php echo $pedido['id']; ?>)</h2>
                <p class="h2 fw-bold text-dark mb-0">R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?></p>
            </div>
            
            <!-- Corpo do Card -->
            <div class="card-body p-4 text-center">
                <h3 class="h4 mb-3"><i class="bi bi-qr-code-scan"></i> Pague com PIX</h3>
                <p class="text-muted">Digitalize o QR Code abaixo com seu app de pagamentos:</p>
                
                <!-- QR Code -->
                <img src="<?php echo $qr_code_imagem_url; ?>" alt="QR Code PIX Fictício" class="img-fluid border rounded p-2" style="max-width: 250px;">
                
                <p class="mt-4 mb-2">Ou copie o código:</p>
                
                <!-- Código Copia e Cola -->
                <div class="input-group mb-3">
                    <input type="text" class="form-control" value="<?php echo $pix_copia_cola; ?>" readonly id="pix-codigo">
                    <button class="btn btn-outline-secondary" type="button" id="btn-copiar-pix" title="Copiar código">
                        <i class="bi bi-clipboard-check"></i>
                    </button>
                </div>

                <!-- Botão de Confirmação -->
                <a href="minha_conta.php?status=pagamento_processando" class="btn btn-primary btn-lg w-100 mt-3">
                    <i class="bi bi-check-circle-fill"></i> Já paguei, ir para meus pedidos
                </a>
            </div>
        </div>
    </div>
</div>

<!-- =============================================== -->
<!-- FIM DO CONTEÚDO DA PÁGINA -->
<!-- =============================================== -->

<!-- Adiciona o script para o botão "Copiar" -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnCopiar = document.getElementById('btn-copiar-pix');
    const inputCodigo = document.getElementById('pix-codigo');

    if (btnCopiar) {
        btnCopiar.addEventListener('click', () => {
            inputCodigo.select();
            // document.execCommand('copy') é mais compatível que navigator.clipboard
            try {
                document.execCommand('copy');
                // Feedback visual
                btnCopiar.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
                setTimeout(() => {
                    btnCopiar.innerHTML = '<i class="bi bi-clipboard-check"></i>';
                }, 2000);
            } catch (err) {
                console.error('Falha ao copiar texto: ', err);
            }
        });
    }
});
</script>

<?php
// 6. INCLUI O NOVO RODAPÉ BOOTSTRAP
// O footer_public.php já inclui o cart.js (para o header)
require_once 'includes/footer_public.php';
?>