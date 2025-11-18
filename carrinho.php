<?php
// 1. O GUARDIÃO! (O header_public.php vai iniciar a sessão)
// O session_start() foi MOVIDO para o header_public.php

// 2. BUSCA OS DADOS DO UTILIZADOR
require_once 'config/database.php';

// INCLUI O NOVO CABEÇALHO BOOTSTRAP
// A sessão é iniciada DENTRO deste arquivo
$titulo_pagina = "Meu Carrinho"; 
require_once 'includes/header_public.php';

// Se o header_public não iniciou a sessão, ou o ID não está lá, algo está errado
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['redirect_url_apos_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php?erro=carrinho_login");
    exit();
}

$id_usuario_logado = $_SESSION['id_usuario'];
try {
    $stmt_usuario = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt_usuario->execute([':id' => $id_usuario_logado]);
    $usuario = $stmt_usuario->fetch();
    if (!$usuario) {
        session_destroy();
        header("Location: login.php?erro=usuario_invalido");
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}

$endereco_salvo = htmlspecialchars($usuario['endereco_rua']) . ', ' . htmlspecialchars($usuario['endereco_numero']) . ' - ' . htmlspecialchars($usuario['endereco_bairro']) . ', ' . htmlspecialchars($usuario['endereco_cidade']) . ' - ' . htmlspecialchars($usuario['endereco_estado']);
?>

<!-- =============================================== -->
<!-- INÍCIO DO CONTEÚDO DA PÁGINA (Refatorado) -->
<!-- =============================================== -->
<div class="row g-4">
    
    <!-- Coluna da Esquerda (Itens do Carrinho) -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h2 class="h4 mb-0">Itens no seu Carrinho</h2>
            </div>
            <div class="card-body">
                <!-- O JavaScript vai preencher aqui -->
                <div id="cart-items">
                    <!-- ... JS preenche aqui ... -->
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna da Direita (Resumo e Checkout) -->
    <div class="col-lg-4">
        <!-- Div do Checkout (com ID corrigido) -->
        <div class="card shadow-sm border-0" id="checkout-form">
            <div class="card-header bg-white py-3">
                <h3 class="h5 mb-0">Resumo do Pedido</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-5 text-muted">Total:</span>
                    <span class="fs-4 fw-bold text-dark" id="cart-total">R$ 0,00</span>
                </div>
                
                <h5 class="h6 mt-4">Dados de Entrega</h5>
                <ul class="list-group list-group-flush text-muted small">
                    <li class="list-group-item px-0">
                        <strong>Cliente:</strong> <?php echo htmlspecialchars($usuario['nome_loja']); ?>
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Telefone:</strong> <?php echo htmlspecialchars($usuario['telefone']); ?>
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Endereço:</strong> <?php echo $endereco_salvo; ?>
                    </li>
                </ul>

                <button type="button" id="concluir-pedido-btn" class="btn btn-primary w-100 btn-lg mt-3">
                    <i class="bi bi-shield-check-fill"></i> Concluir pedido
                </button>
            </div>
        </div>

        <!-- Div de Sucesso (escondida) -->
        <!-- ESTE DIV NÃO É MAIS USADO, MAS PODE FICAR -->
        <div id="checkout-sucesso" style="display: none;" class="alert alert-success text-center mt-3 p-4 shadow-sm">
        </div>
    </div>
</div>

<!-- =============================================== -->
<!-- FIM DO CONTEÚDO DA PÁGINA -->
<!-- =============================================== -->

<!-- =============================================== -->
<!-- SCRIPT ATUALIZADO (Refatorado para Bootstrap) -->
<!-- =============================================== -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- 1. SELETORES (Corrigido) ---
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    const checkoutForm = document.getElementById('checkout-form'); // Agora vai funcionar
    const concluirBtn = document.getElementById('concluir-pedido-btn');
    const cart = JSON.parse(localStorage.getItem('shoplinkCart')) || [];

    // --- 2. FUNÇÃO PRINCIPAL PARA MOSTRAR O CARRINHO ---
    function displayCart() {
        cartItemsContainer.innerHTML = ''; // Limpa o container
        let total = 0;
        let validItemsInCart = 0; // Contador de itens válidos

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p class="text-muted">Seu carrinho está vazio.</p>';
            cartTotalElement.innerText = 'R$ 0,00';
            if (checkoutForm) { 
                checkoutForm.style.display = 'none'; 
            }
            return;
        }
        
        if (checkoutForm) { 
            checkoutForm.style.display = 'block'; 
        }
        
        const listGroup = document.createElement('ul');
        listGroup.className = 'list-group list-group-flush';

        cart.forEach(item => {
            if (!item || item.preco === undefined || item.preco === null) {
                console.warn("Item corrompido no carrinho, ignorando:", item);
                return; 
            }
            validItemsInCart++;
            const itemTotal = item.preco * item.quantity;
            total += itemTotal;
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex align-items-center px-0';
            li.innerHTML = `
                <img src="uploads/${item.imagem}" alt="${item.nome}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;" class="me-3">
                <div class="flex-grow-1">
                    <h5 class="mb-0 fs-6">${item.nome}</h5>
                    <small class="text-muted">Qtd: ${item.quantity} x R$ ${item.preco.toFixed(2).replace('.', ',')}</small>
                </div>
                <strong class="fs-5 ms-3">R$ ${itemTotal.toFixed(2).replace('.', ',')}</strong>
                <button class="btn btn-sm btn-outline-danger ms-3 remove-item-btn" data-id="${item.id}" title="Remover item">
                    <i class="bi bi-trash-fill"></i>
                </button>
            `;
            listGroup.appendChild(li);
        });

        cartItemsContainer.appendChild(listGroup);
        cartTotalElement.innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
        addRemoveEvents();

        if (validItemsInCart === 0) {
            cartItemsContainer.innerHTML = '<p class="text-muted">Seu carrinho está vazio (itens inválidos foram removidos).</p>';
            cartTotalElement.innerText = 'R$ 0,00';
            if (checkoutForm) { 
                checkoutForm.style.display = 'none'; 
            }
        }
    }
    
    // --- 3. FUNÇÕES DE LÓGICA DO CARRINHO ---
    function saveCart() { localStorage.setItem('shoplinkCart', JSON.stringify(cart)); }
    function addRemoveEvents() {
        const removeButtons = document.querySelectorAll('.remove-item-btn');
        removeButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const productId = event.currentTarget.dataset.id;
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
    
    // --- 4. FUNÇÃO DE CHECKOUT (ENVIO DO PEDIDO) ---
    concluirBtn.addEventListener('click', async () => {
        concluirBtn.disabled = true;
        concluirBtn.innerText = 'Processando...';
        const pedidoData = { carrinho: cart };

        try {
            const response = await fetch('salvar_pedido_logado.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(pedidoData)
            });
            const result = await response.json();

            // --- MUDANÇA CRÍTICA AQUI ---
            if (result.sucesso) {
                // SUCESSO!
                
                // 1. Limpa o carrinho do localStorage
                localStorage.removeItem('shoplinkCart');
                
                // 2. Redireciona para a página de pagamento
                window.location.href = `pagamento.php?id_pedido=${result.id_pedido}`;

            } else {
                // Se der erro, mostra o alerta
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

<?php
// 4. INCLUI O NOVO RODAPÉ BOOTSTRAP
require_once 'includes/footer_public.php';
?>