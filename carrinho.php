<?php
// 1. O GUARDIÃO!
session_start();

// Se não estiver logado, chuta para o login
if (!isset($_SESSION['id_usuario'])) {
    // Salva a URL atual para redirecionar de volta para o carrinho após o login
    $_SESSION['redirect_url_apos_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php?erro=carrinho_login");
    exit();
}

// 2. SE ESTÁ LOGADO, BUSCA OS DADOS DO UTILIZADOR
require_once 'config/database.php';
$id_usuario_logado = $_SESSION['id_usuario'];

try {
    // Busca os dados do cliente (para o endereço)
    $stmt_usuario = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt_usuario->execute([':id' => $id_usuario_logado]);
    $usuario = $stmt_usuario->fetch();

    if (!$usuario) {
        // Se o utilizador da sessão não for encontrado no banco, destrói a sessão
        session_destroy();
        header("Location: login.php?erro=usuario_invalido");
        exit();
    }

} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}

// Formata o endereço salvo para exibição
$endereco_salvo = htmlspecialchars($usuario['endereco_rua']) . ', ' . htmlspecialchars($usuario['endereco_numero']) . ' - ' . htmlspecialchars($usuario['endereco_bairro']) . ', ' . htmlspecialchars($usuario['endereco_cidade']) . ' - ' . htmlspecialchars($usuario['endereco_estado']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho - Shoplink</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .cart-item { display: flex; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .cart-item img { width: 80px; height: 80px; object-fit: cover; margin-right: 15px; border-radius: 5px; }
        .cart-item-info { flex-grow: 1; }
        .cart-item-info h4 { margin: 0 0 5px 0; }
        .checkout-form { background-color: #fff; padding: 20px; border-radius: 8px; margin-top: 20px; }
        
        #concluir-pedido-btn { background-color: #3498db; color: white; padding: 15px 20px; border: none; border-radius: 5px; font-size: 1.2em; cursor: pointer; width: 100%; margin-top: 10px; transition: background-color 0.3s; }
        #concluir-pedido-btn:hover { background-color: #2980b9; }
        #concluir-pedido-btn:disabled { background-color: #aaa; cursor: not-allowed; }
        
        .remove-item-btn { color: #e74c3c; text-decoration: none; font-size: 0.9em; font-weight: bold; }
        .remove-item-btn:hover { text-decoration: underline;}
        
        .customer-data-box { background-color: #f9f9f9; border: 1px solid #eee; border-radius: 8px; padding: 15px; }
        .customer-data-box p { margin: 5px 0; }
    </style>
</head>
<body>
    <header class="main-header">
        <h1>Meu Carrinho</h1>
        <a href="index.php" style="color: white; text-decoration: none;">&larr; Voltar ao Catálogo</a>
    </header>

    <main class="container">
        <div id="cart-items">
            </div>

        <h3 id="cart-total">Total: R$ 0,00</h3>

        <div id="checkout-sucesso" style="display: none; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; text-align: center;">
            </div>

        <div class="checkout-form" id="checkout-form">
            <h3>Finalizar Pedido</h3>
            
            <div class="customer-data-box">
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($usuario['nome_loja']); ?></p>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($usuario['telefone']); ?></p>
                <p><strong>Entregar em:</strong> <?php echo $endereco_salvo; ?></p>
                </div>
            
            <button type="button" id="concluir-pedido-btn">Concluir pedido</button>
        </div>
    </main>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- 1. SELETORES E DADOS ---
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');
        const checkoutForm = document.getElementById('checkout-form');
        const concluirBtn = document.getElementById('concluir-pedido-btn');

        const cart = JSON.parse(localStorage.getItem('shoplinkCart')) || [];

        // --- 2. FUNÇÃO PRINCIPAL PARA MOSTRAR O CARRINHO ---
        function displayCart() {
            cartItemsContainer.innerHTML = ''; // Limpa o container
            let total = 0;

            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<p>Seu carrinho está vazio.</p>';
                cartTotalElement.style.display = 'none';
                checkoutForm.style.display = 'none';
                return;
            }

            cartTotalElement.style.display = 'block';
            checkoutForm.style.display = 'block';

            cart.forEach(item => {
                const itemTotal = item.preco * item.quantity;
                total += itemTotal;
                
                cartItemsContainer.innerHTML += `
                    <div class="cart-item">
                        <img src="uploads/${item.imagem}" alt="${item.nome}">
                        <div class="cart-item-info">
                            <h4>${item.nome}</h4>
                            <p>Quantidade: ${item.quantity}</p>
                            <p>Preço: R$ ${item.preco.toFixed(2).replace('.', ',')}</p>
                            <a href="#" class="remove-item-btn" data-id="${item.id}">Remover</a>
                        </div>
                        <strong>R$ ${itemTotal.toFixed(2).replace('.', ',')}</strong>
                    </div>
                `;
            });
            cartTotalElement.innerText = `Total: R$ ${total.toFixed(2).replace('.', ',')}`;
            addRemoveEvents();
        }
        
        // --- 3. FUNÇÕES DE LÓGICA DO CARRINHO ---
        function saveCart() {
            localStorage.setItem('shoplinkCart', JSON.stringify(cart));
        }
        function addRemoveEvents() {
            const removeButtons = document.querySelectorAll('.remove-item-btn');
            removeButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const productId = event.target.dataset.id;
                    removeItemFromCart(productId);
                });
            });
        }
        function removeItemFromCart(productId) {
            const itemIndex = cart.findIndex(item => item.id === productId);
            if (itemIndex > -1) { cart.splice(itemIndex, 1); }
            saveCart();
            displayCart();
        }
        function htmlspecialchars(str) {
             return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        // --- 4. FUNÇÃO DE CHECKOUT (ENVIO DO PEDIDO) ---
        concluirBtn.addEventListener('click', async () => {
            concluirBtn.disabled = true;
            concluirBtn.innerText = 'Processando...';

            // Agora, o único dado que enviamos é o carrinho.
            // O backend vai descobrir QUEM está logado pela $_SESSION.
            const pedidoData = {
                carrinho: cart
            };

            try {
                // Vamos chamar um novo script, mais seguro
                const response = await fetch('salvar_pedido_logado.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(pedidoData)
                });
                
                const result = await response.json();

                if (result.sucesso) {
                    // LIMPA O CARRINHO
                    localStorage.removeItem('shoplinkCart');
                    
                    // --- O FLUXO DE PAGAMENTO COMEÇA AQUI ---
                    // Redireciona para a nova página de pagamento, passando o ID do pedido
                    window.location.href = `pagamento.php?id_pedido=${result.id_pedido}`;

                } else {
                    alert('Erro ao processar o pedido: ' + (result.mensagem || 'Tente novamente.'));
                    concluirBtn.disabled = false;
                    concluirBtn.innerText = 'Concluir pedido';
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                alert('Não foi possível conectar ao servidor. Tente novamente.');
                concluirBtn.disabled = false;
                concluirBtn.innerText = 'Concluir pedido';
            }
        });
        
        // --- 5. INICIALIZAÇÃO ---
        displayCart();
        
    }); // Fim do 'DOMContentLoaded'
    </script>
</body>
</html>