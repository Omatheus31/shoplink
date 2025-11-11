<?php
// Adicionamos a conexão com o BD para buscar o número do WhatsApp
require_once 'config/database.php';

try {
    $stmt = $pdo->query("SELECT valor FROM configuracoes WHERE chave = 'whatsapp_numero'");
    $numeroLoja = $stmt->fetchColumn();
} catch (PDOException $e) {
    // Se der erro, usa um número padrão para não quebrar a funcionalidade
    $numeroLoja = '5500000000000'; // Número padrão em caso de erro
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho - Shoplink</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* [Seus estilos CSS daqui para cima não mudam] */
        .cart-item { display: flex; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .cart-item img { width: 80px; height: 80px; object-fit: cover; margin-right: 15px; border-radius: 5px; }
        .cart-item-info { flex-grow: 1; }
        .cart-item-info h4 { margin: 0 0 5px 0; }
        .checkout-form { background-color: #fff; padding: 20px; border-radius: 8px; margin-top: 20px; }
        #whatsapp-btn { background-color: #174269ff; color: white; padding: 15px 20px; border: none; border-radius: 5px; font-size: 1.2em; cursor: pointer; width: 100%; margin-top: 10px; transition: background-color 0.3s; }
        #whatsapp-btn:hover { background-color: #128C7E; }
        #whatsapp-btn:disabled { background-color: #aaa; cursor: not-allowed; }
        .remove-item-btn { color: #e74c3c; text-decoration: none; font-size: 0.9em; font-weight: bold; }
        .remove-item-btn:hover { text-decoration: underline;}
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
        <div class="checkout-form">
            <h3>Finalizar Pedido</h3>
            <form id="checkout-form">
                <div>
                    <label for="nome">Seu Nome:</label>
                    <input type="text" id="nome" name="nome" required style="width: 95%; padding: 8px; margin-bottom: 10px;">
                </div>
                <div>
                    <label for="telefone">WhatsApp / Telefone (com DDD):</label>
                    <input type="tel" id="telefone" name="telefone" required style="width: 95%; padding: 8px; margin-bottom: 10px;" placeholder="Ex: 93912345678">
                </div>
                <div>
                    <label for="endereco">Endereço de Entrega:</label>
                    <textarea id="endereco" name="endereco" required style="width: 95%; padding: 8px;"></textarea>
                </div>
                <button type="submit" id="whatsapp-btn">Concluir pedido</button>
            </form>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- 1. SELETORES E DADOS ---
            const cartItemsContainer = document.getElementById('cart-items');
            const cartTotalElement = document.getElementById('cart-total');
            const checkoutForm = document.getElementById('checkout-form');
            const whatsappBtn = document.getElementById('whatsapp-btn');
            const cart = JSON.parse(localStorage.getItem('shoplinkCart')) || [];

            // --- 2. FUNÇÃO PRINCIPAL PARA MOSTRAR O CARRINHO ---
            function displayCart() {
                cartItemsContainer.innerHTML = '';
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
                    const cartItemHTML = `
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
                    cartItemsContainer.innerHTML += cartItemHTML;
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
                if (itemIndex > -1) {
                    cart.splice(itemIndex, 1);
                }
                saveCart();
                displayCart();
            }

            // --- 4. FUNÇÃO DE CHECKOUT (ENVIO DO PEDIDO) ---
            checkoutForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                whatsappBtn.disabled = true;
                whatsappBtn.innerText = 'Processando...';

                const clienteNome = document.getElementById('nome').value;
                const clienteTelefone = document.getElementById('telefone').value;
                const clienteEndereco = document.getElementById('endereco').value;

                const pedidoData = {
                    cliente: {
                        nome: clienteNome,
                        telefone: clienteTelefone,
                        endereco: clienteEndereco
                    },
                    carrinho: cart
                };

                try {
                    const response = await fetch('salvar_pedido.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(pedidoData)
                    });

                    const result = await response.json();

                    if (result.sucesso) {
                        localStorage.removeItem('shoplinkCart');
                        document.getElementById('cart-items').style.display = 'none';
                        document.getElementById('cart-total').style.display = 'none';
                        document.getElementById('checkout-form').style.display = 'none';

                        const sucessoDiv = document.getElementById('checkout-sucesso');
                        sucessoDiv.innerHTML = `
                            <h3>✅ Pedido Nº ${result.id_pedido} Recebido!</h3>
                            <p>Obrigado, ${htmlspecialchars(result.nome_cliente)}! Já estamos separando seu pedido.</p>
                            <p>Entraremos em contato em breve pelo número <strong>${htmlspecialchars(clienteTelefone)}</strong> para confirmar o pagamento e a entrega.</p>
                            <br>
                            <a href="index.php" class="add-to-cart-btn" style="background-color: #27ae60;">Voltar ao Catálogo</a>
                        `;
                        sucessoDiv.style.display = 'block';

                    } else {
                        // --- ERRO DE SINTAXE CORRIGIDO AQUI (FALTAVA UM '+') ---
                        alert('Erro ao processar o pedido: ' + (result.mensagem || 'Tente novamente.'));
                        whatsappBtn.disabled = false;
                        whatsappBtn.innerText = 'Finalizar Pedido';
                    }
                } catch (error) {
                    // Erro de rede/conexão
                    console.error('Erro na requisição:', error);
                    alert('Não foi possível conectar ao servidor. Tente novamente.');
                    whatsappBtn.disabled = false;
                    whatsappBtn.innerText = 'Finalizar Pedido';
                }
            });

            // Função de segurança para evitar XSS
            function htmlspecialchars(str) {
                return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            }
            
            // --- 5. INICIALIZAÇÃO ---
            displayCart();
            
        }); // Fim do 'DOMContentLoaded'
    </script>