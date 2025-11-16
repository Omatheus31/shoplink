document.addEventListener('DOMContentLoaded', () => {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    let cart = JSON.parse(localStorage.getItem('shoplinkCart')) || [];

    addToCartButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const productId = event.currentTarget.dataset.id;
            const productNome = event.currentTarget.dataset.nome;
            const productPreco = parseFloat(event.currentTarget.dataset.preco);
            const productImagem = event.currentTarget.dataset.imagem;

            const productInCart = cart.find(item => item.id === productId);

            if (productInCart) {
                productInCart.quantity++;
            } else {
                cart.push({
                    id: productId,
                    nome: productNome,
                    preco: productPreco,
                    imagem: productImagem,
                    quantity: 1
                });
            }

            saveCart();
            showToast(`"${productNome}" foi adicionado!`);
            updateCartCounter();
        });
    });

    function saveCart() {
        localStorage.setItem('shoplinkCart', JSON.stringify(cart));
    }

    let toastTimer;
    function showToast(message) {
        const toast = document.getElementById('toast-notification');
        if (!toast) return;

        toast.innerText = message;
        toast.classList.add('show');

        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.remove('show');
        }, 3000); // 3 segundos
    }

    function updateCartCounter() {
        const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
        const counterElement = document.getElementById('cart-counter');
        if (counterElement) {
            counterElement.innerText = cartCount;
        }
    }

    // Atualiza o contador assim que a página carrega
    updateCartCounter();
});