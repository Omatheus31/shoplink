<?php
// carrinho.php
require_once 'config/database.php';
$titulo_pagina = "Meu Carrinho"; 
require_once 'includes/header_public.php';

// Verifica Login
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
    die("Erro: " . $e->getMessage());
}

// Formata endereço
$endereco_salvo = htmlspecialchars($usuario['endereco_rua'] ?? '') . ', ' . 
                  htmlspecialchars($usuario['endereco_numero'] ?? '') . ' - ' . 
                  htmlspecialchars($usuario['endereco_bairro'] ?? '') . ', ' . 
                  htmlspecialchars($usuario['endereco_cidade'] ?? '');
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white py-3">
                <h2 class="h5 mb-0"><i class="bi bi-bag"></i> Itens no Carrinho</h2>
            </div>
            <div class="card-body">
                <div id="cart-items">
                    </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0" id="checkout-form">
            <div class="card-header bg-white py-3">
                <h3 class="h5 mb-0"><i class="bi bi-wallet2"></i> Finalizar Compra</h3>
            </div>
            <div class="card-body">
                
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                    <span class="text-muted">Total do Pedido:</span>
                    <span class="fs-4 fw-bold text-success" id="cart-total">R$ 0,00</span>
                </div>
                
                <h6 class="text-muted small text-uppercase fw-bold mt-3">Entrega para:</h6>
                <div class="mb-3 small">
                    <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong><br>
                    <span class="text-muted"><?php echo $endereco_salvo; ?></span>
                </div>

                <h6 class="text-muted small text-uppercase fw-bold mt-4 mb-2">Forma de Pagamento:</h6>
                <div class="d-grid gap-2 mb-4">
                    <div class="form-check border rounded p-3 d-flex align-items-center cursor-pointer">
                        <input class="form-check-input mt-0" type="radio" name="metodo_pagamento" id="pag_pix" value="PIX" checked>
                        <label class="form-check-label ms-2 w-100" for="pag_pix">
                            <i class="bi bi-qr-code text-success me-2"></i> PIX (Imediato)
                        </label>
                    </div>
                    <div class="form-check border rounded p-3 d-flex align-items-center cursor-pointer">
                        <input class="form-check-input mt-0" type="radio" name="metodo_pagamento" id="pag_cartao" value="Cartão de Crédito">
                        <label class="form-check-label ms-2 w-100" for="pag_cartao">
                            <i class="bi bi-credit-card text-primary me-2"></i> Cartão de Crédito
                        </label>
                    </div>
                    <div class="form-check border rounded p-3 d-flex align-items-center cursor-pointer">
                        <input class="form-check-input mt-0" type="radio" name="metodo_pagamento" id="pag_boleto" value="Boleto">
                        <label class="form-check-label ms-2 w-100" for="pag_boleto">
                            <i class="bi bi-upc-scan text-dark me-2"></i> Boleto Bancário
                        </label>
                    </div>
                </div>

                <button type="button" id="concluir-pedido-btn" class="btn btn-success w-100 btn-lg py-3 shadow-sm">
                    <i class="bi bi-check-lg"></i> Confirmar Pedido
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRemoveItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <div class="mb-3 text-warning"><i class="bi bi-exclamation-triangle-fill fs-1"></i></div>
        <h5 class="fw-bold mb-2">Remover item?</h5>
        <p class="text-muted small mb-4">Você tem certeza que deseja tirar este produto do carrinho?</p>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="btn-confirm-remove">Sim, remover</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    const checkoutForm = document.getElementById('checkout-form');
    const concluirBtn = document.getElementById('concluir-pedido-btn');
    
    // Modal
    const modalEl = document.getElementById('modalRemoveItem');
    const modalRemove = new bootstrap.Modal(modalEl);
    const btnConfirmRemove = document.getElementById('btn-confirm-remove');
    let itemToRemoveIndex = null; // Guarda qual item vamos apagar

    let cart = JSON.parse(localStorage.getItem('shoplinkCart')) || [];

    function saveCart() {
        localStorage.setItem('shoplinkCart', JSON.stringify(cart));
        displayCart();
        updateCartCounter();
    }

    function displayCart() {
        cartItemsContainer.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<div class="text-center py-5"><i class="bi bi-cart-x fs-1 text-muted"></i><p class="mt-3">Seu carrinho está vazio.</p><a href="index.php" class="btn btn-outline-primary btn-sm">Ir às compras</a></div>';
            cartTotalElement.innerText = 'R$ 0,00';
            if (checkoutForm) checkoutForm.style.display = 'none';
            return;
        }
        
        if (checkoutForm) checkoutForm.style.display = 'block';
        
        const listGroup = document.createElement('ul');
        listGroup.className = 'list-group list-group-flush';

        cart.forEach((item, index) => {
            const itemTotal = item.preco * item.quantity;
            total += itemTotal;
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex align-items-center px-0 py-3 border-bottom flex-wrap';
            li.innerHTML = `
                <div class="d-flex align-items-center flex-grow-1">
                    <img src="uploads/${item.imagem}" alt="${item.nome}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" class="me-3 border">
                    <div>
                        <h6 class="mb-1 text-truncate" style="max-width: 180px;">${item.nome}</h6>
                        <small class="text-muted">Unit: R$ ${item.preco.toFixed(2).replace('.', ',')}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center mt-2 mt-sm-0">
                    <div class="input-group input-group-sm me-3" style="width: 100px;">
                        <button class="btn btn-outline-secondary btn-decrease" data-index="${index}" type="button"><i class="bi bi-dash"></i></button>
                        <input type="text" class="form-control text-center bg-white" value="${item.quantity}" readonly>
                        <button class="btn btn-outline-secondary btn-increase" data-index="${index}" type="button"><i class="bi bi-plus"></i></button>
                    </div>
                    <strong class="text-dark me-3" style="min-width: 80px; text-align: right;">R$ ${itemTotal.toFixed(2).replace('.', ',')}</strong>
                    <button class="btn btn-sm text-danger remove-item-btn" data-index="${index}" title="Remover">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
            `;
            listGroup.appendChild(li);
        });

        cartItemsContainer.appendChild(listGroup);
        cartTotalElement.innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
        addEvents();
    }

    function addEvents() {
        document.querySelectorAll('.btn-increase').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = e.currentTarget.dataset.index;
                cart[index].quantity++;
                saveCart();
            });
        });

        document.querySelectorAll('.btn-decrease').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = e.currentTarget.dataset.index;
                if (cart[index].quantity > 1) {
                    cart[index].quantity--;
                    saveCart();
                } else {
                    // Abre o modal se for diminuir de 1 para 0
                    itemToRemoveIndex = index;
                    modalRemove.show();
                }
            });
        });

        document.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Abre o modal ao clicar na lixeira
                itemToRemoveIndex = e.currentTarget.dataset.index;
                modalRemove.show();
            });
        });
    }

    // Ação do botão "Sim, remover" do Modal
    btnConfirmRemove.addEventListener('click', () => {
        if (itemToRemoveIndex !== null) {
            cart.splice(itemToRemoveIndex, 1);
            saveCart();
            modalRemove.hide();
            itemToRemoveIndex = null;
        }
    });

    function updateCartCounter() {
        const counter = document.getElementById('cart-counter');
        if(counter) {
            const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
            counter.innerText = totalQty;
        }
    }

    concluirBtn.addEventListener('click', async () => {
        const metodoPagamentoEl = document.querySelector('input[name="metodo_pagamento"]:checked');
        if (!metodoPagamentoEl) { alert("Selecione uma forma de pagamento."); return; }
        
        concluirBtn.disabled = true;
        concluirBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processando...';
        
        try {
            const response = await fetch('salvar_pedido_logado.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ carrinho: cart, metodo_pagamento: metodoPagamentoEl.value })
            });
            const result = await response.json();

            if (result.sucesso) {
                localStorage.removeItem('shoplinkCart');
                window.location.href = `pagamento.php?id_pedido=${result.id_pedido}`;
            } else {
                alert('Erro: ' + (result.mensagem || 'Tente novamente.'));
                concluirBtn.disabled = false;
                concluirBtn.innerText = 'Confirmar Pedido';
            }
        } catch (error) {
            console.error(error);
            alert('Erro de conexão.');
            concluirBtn.disabled = false;
            concluirBtn.innerText = 'Confirmar Pedido';
        }
    });
    
    displayCart();
});
</script>

<?php require_once 'includes/footer_public.php'; ?>