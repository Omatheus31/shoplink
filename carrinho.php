<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho - Shoplink</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Estilos específicos para a página do carrinho */
        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 5px;
        }
        .cart-item-info {
            flex-grow: 1;
        }
        .cart-item-info h4 {
            margin: 0 0 5px 0;
        }
        .checkout-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        #whatsapp-btn {
            background-color: #25D366;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        #whatsapp-btn:hover {
            background-color: #128C7E;
        }
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
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');
        const checkoutForm = document.getElementById('checkout-form');

        // Carrega o carrinho do localStorage
        const cart = JSON.parse(localStorage.getItem('shoplinkCart')) || [];

        function displayCart() {
            cartItemsContainer.innerHTML = ''; // Limpa o container
            let total = 0;

            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<p>Seu carrinho está vazio.</p>';
                cartTotalElement.style.display = 'none';
                checkoutForm.style.display = 'none';
                return;
            }

            cart.forEach(item => {
                const itemTotal = item.preco * item.quantity;
                total += itemTotal;

                // Cria o HTML para cada item do carrinho
                const cartItemHTML = `
                    <div class="cart-item">
                        <img src="uploads/${item.imagem}" alt="${item.nome}">
                        <div class="cart-item-info">
                            <h4>${item.nome}</h4>
                            <p>Quantidade: ${item.quantity}</p>
                            <p>Preço: R$ ${item.preco.toFixed(2).replace('.', ',')}</p>
                        </div>
                        <strong>R$ ${itemTotal.toFixed(2).replace('.', ',')}</strong>
                    </div>
                `;
                cartItemsContainer.innerHTML += cartItemHTML;
            });

            // Atualiza o valor total no HTML
            cartTotalElement.innerText = `Total: R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        
        // Lida com o envio do formulário
        checkoutForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Impede o envio padrão do formulário

            const nomeCliente = document.getElementById('nome').value;
            const enderecoCliente = document.getElementById('endereco').value;
            
            // Pega o número do WhatsApp da loja (substitua pelo seu número)
            // No futuro, este número virá do banco de dados
            const numeroLoja = '5593991337352'; // IMPORTANTE: Coloque seu número com código do país (55) e DDD.

            // --- Monta a mensagem para o WhatsApp ---
            let mensagem = `Olá! 👋 Gostaria de fazer um pedido:\n\n`;
            
            cart.forEach(item => {
                mensagem += `*Produto:* ${item.nome}\n`;
                mensagem += `*Quantidade:* ${item.quantity}\n`;
                mensagem += `*Preço Unitário:* R$ ${item.preco.toFixed(2).replace('.', ',')}\n\n`;
            });
            
            const totalPedido = cart.reduce((total, item) => total + (item.preco * item.quantity), 0);
            mensagem += `*Total do Pedido: R$ ${totalPedido.toFixed(2).replace('.', ',')}*\n\n`;
            mensagem += `--- DADOS PARA ENTREGA ---\n`;
            mensagem += `*Nome:* ${nomeCliente}\n`;
            mensagem += `*Endereço:* ${enderecoCliente}`;
            
            // Codifica a mensagem para ser usada em uma URL
            const mensagemCodificada = encodeURIComponent(mensagem);
            
            // Cria o link do WhatsApp e redireciona o usuário
            const whatsappUrl = `https://api.whatsapp.com/send?phone=${numeroLoja}&text=${mensagemCodificada}`;
            
            window.location.href = whatsappUrl;
        });
        
        // Exibe o carrinho assim que a página carrega
        displayCart();
    });
    </script>
</body>
</html>