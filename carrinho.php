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
        #whatsapp-btn { background-color: #25D366; color: white; padding: 15px 20px; border: none; border-radius: 5px; font-size: 1.2em; cursor: pointer; width: 100%; margin-top: 10px; transition: background-color 0.3s; }
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

        <div class="checkout-form">
            <h3>Finalizar Pedido</h3>
            <form id="checkout-form">
                <div>
                    <label for="nome">Seu Nome:</label>
                    <input type="text" id="nome" name="nome" required style="width: 95%; padding: 8px; margin-bottom: 10px;">
                </div>
                <div>
                    <label for="endereco">Endereço de Entrega:</label>
                    <textarea id="endereco" name="endereco" required style="width: 95%; padding: 8px;"></textarea>
                </div>
                <button type="submit" id="whatsapp-btn">Pedir por WhatsApp</button>
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

            // Carrega o carrinho do localStorage
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

                // Exibe o formulário se tiver itens
                cartTotalElement.style.display = 'block';
                checkoutForm.style.display = 'block';

                cart.forEach(item => {
                    const itemTotal = item.preco * item.quantity;
                    total += itemTotal;

                    // --- HTML CORRIGIDO COM O BOTÃO "REMOVER" ---
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

                // Atualiza o valor total
                cartTotalElement.innerText = `Total: R$ ${total.toFixed(2).replace('.', ',')}`;
                
                // Adiciona os eventos aos botões "Remover" que acabamos de criar
                addRemoveEvents();
            }
            
            // --- 3. FUNÇÕES DE LÓGICA DO CARRINHO ---

            // Função para salvar o carrinho no localStorage
            function saveCart() {
                localStorage.setItem('shoplinkCart', JSON.stringify(cart));
            }

            // Função para adicionar eventos de clique aos botões "Remover"
            function addRemoveEvents() {
                const removeButtons = document.querySelectorAll('.remove-item-btn');
                removeButtons.forEach(button => {
                    button.addEventListener('click', (event) => {
                        event.preventDefault(); // Impede que o link '#' recarregue a página
                        const productId = event.target.dataset.id;
                        removeItemFromCart(productId);
                    });
                });
            }

            // Função para remover um item do array 'cart'
            function removeItemFromCart(productId) {
                // Encontra o índice (aposição) do item no array
                const itemIndex = cart.findIndex(item => item.id === productId);

                if (itemIndex > -1) { // Se o item foi encontrado...
                    cart.splice(itemIndex, 1); // Remove 1 item daquela posição
                }
                saveCart(); // Salva o carrinho atualizado
                displayCart(); // Redesenha o carrinho na tela
            }

            // --- 4. FUNÇÃO DE CHECKOUT (ENVIO DO PEDIDO) ---
            checkoutForm.addEventListener('submit', async (event) => {
                event.preventDefault(); // Impede o envio padrão do formulário
                
                whatsappBtn.disabled = true;
                whatsappBtn.innerText = 'Processando...';

                const pedidoData = {
                    cliente: {
                        nome: document.getElementById('nome').value,
                        endereco: document.getElementById('endereco').value
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
                        const numeroLoja = '<?php echo $numeroLoja; ?>';
                        let mensagem = `Olá! 👋 Gostaria de fazer um pedido (Nº ${result.id_pedido}):\n\n`;
                        cart.forEach(item => {
                            mensagem += `*Produto:* ${item.nome}\n*Qtd:* ${item.quantity}\n\n`;
                        });
                        const totalPedido = cart.reduce((total, item) => total + (item.preco * item.quantity), 0);
                        mensagem += `*Total: R$ ${totalPedido.toFixed(2).replace('.', ',')}*\n\n`;
                        mensagem += `--- DADOS DE ENTREGA ---\n`;
                        mensagem += `*Nome:* ${pedidoData.cliente.nome}\n`;
                        mensagem += `*Endereço:* ${pedidoData.cliente.endereco}`;
                        
                        const mensagemCodificada = encodeURIComponent(mensagem);
                        const whatsappUrl = `https://api.whatsapp.com/send?phone=${numeroLoja}&text=${mensagemCodificada}`;
                        
                        localStorage.removeItem('shoplinkCart');
                        window.location.href = whatsappUrl;

                    } else {
                        alert('Erro ao processar o pedido: ' + result.mensagem);
                        whatsappBtn.disabled = false;
                        whatsappBtn.innerText = 'Pedir por WhatsApp';
                    }
                } catch (error) {
                    console.error('Erro na requisição:', error);
                    alert('Não foi possível conectar ao servidor. Tente novamente.');
                    whatsappBtn.disabled = false;
                    whatsappBtn.innerText = 'Pedir por WhatsApp';
                }
            });
            
            // --- 5. INICIALIZAÇÃO ---
            // Exibe o carrinho assim que a página carrega
            displayCart();
            
        }); // Fim do 'DOMContentLoaded'
    </script>