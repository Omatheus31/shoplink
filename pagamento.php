<?php
// 1. O GUARDIÃO! (Verifica se o cliente está logado)
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php?erro=acesso_negado");
    exit();
}

// 2. CONEXÃO E BUSCA DE DADOS
require_once 'config/database.php';
$id_usuario_logado = $_SESSION['id_usuario'];

// Verifica se o ID do pedido foi passado
if (!isset($_GET['id_pedido']) || empty($_GET['id_pedido'])) {
    header("Location: index.php"); // Se não tem ID, volta ao catálogo
    exit();
}

$id_pedido = (int)$_GET['id_pedido'];

try {
    // 3. BUSCA O PEDIDO NO BANCO
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
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento do Pedido #<?php echo $pedido['id']; ?> - Shoplink</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .payment-container {
            max-width: 500px;
            margin: 30px auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .payment-header {
            padding: 20px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #eee;
        }
        .payment-header h2 {
            margin: 0;
            font-size: 1.2em;
            color: #555;
        }
        .payment-header .total {
            font-size: 2.2em;
            font-weight: bold;
            color: #2c3e50;
            margin: 5px 0 0 0;
        }
        .payment-body {
            padding: 30px;
        }
        .payment-body h3 {
            margin-top: 0;
            color: #333;
        }
        .payment-body img {
            width: 250px;
            height: 250px;
            border: 2px solid #333;
            border-radius: 5px;
        }
        .pix-copia-cola {
            background-color: #f4f4f4;
            border: 1px dashed #ccc;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9em;
            word-wrap: break-word; /* Quebra o texto longo */
            margin: 15px 0;
        }
        .btn-pago {
            display: inline-block;
            width: 90%;
            background-color: #3498db;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
            font-weight: bold;
        }
        .btn-pago:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <h1>Finalizar Pagamento</h1>
        <a href="index.php" style="color: white; text-decoration: none;">&larr; Voltar ao Catálogo</a>
    </header>

    <main class="container">
        <div class="payment-container">
            <div class="payment-header">
                <h2>Total a pagar (Pedido #<?php echo $pedido['id']; ?>)</h2>
                <p class="total">R$ <?php echo number_format($pedido['total_pedido'], 2, ',', '.'); ?></p>
            </div>
            <div class="payment-body">
                <h3>Pague com PIX para concluir</h3>
                <p>Digitalize o QR Code abaixo:</p>
                <img src="<?php echo $qr_code_imagem_url; ?>" alt="QR Code PIX Fictício">
                
                <p style="margin-top: 25px;">Ou copie o código:</p>
                <div class="pix-copia-cola">
                    <?php echo $pix_copia_cola; ?>
                </div>

                <a href="minha_conta.php?status=pagamento_processando" class="btn-pago">
                    Já paguei, ir para meus pedidos
                </a>
            </div>
        </div>
    </main>
</body>
</html>