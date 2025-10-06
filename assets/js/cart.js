// Aguarda o DOM (a página) ser totalmente carregado para executar o script
document.addEventListener('DOMContentLoaded', () => {
    
    // Seleciona todos os botões "Adicionar ao Carrinho"
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');

    // Recupera o carrinho do localStorage ou cria um array vazio se não existir
    let cart = JSON.parse(localStorage.getItem('shoplinkCart')) || [];

    // Adiciona um "ouvinte" de evento de clique para cada botão
    addToCartButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            
            // Pega os dados do produto a partir dos atributos data-* do botão clicado
            const productId = event.target.dataset.id;
            const productNome = event.target.dataset.nome;
            const productPreco = parseFloat(event.target.dataset.preco);
            const productImagem = event.target.dataset.imagem;

            // Verifica se o produto já está no carrinho
            const productInCart = cart.find(item => item.id === productId);

            if (productInCart) {
                // Se já estiver, apenas incrementa a quantidade
                productInCart.quantity++;
            } else {
                // Se não estiver, adiciona o novo produto com quantidade 1
                cart.push({
                    id: productId,
                    nome: productNome,
                    preco: productPreco,
                    imagem: productImagem,
                    quantity: 1
                });
            }

            // Salva o carrinho atualizado de volta no localStorage
            saveCart();
            
            // Fornece um feedback visual para o usuário
            alert(`"${productNome}" foi adicionado ao carrinho!`);

            // (Opcional) Atualiza um contador de itens no futuro
            updateCartCounter();
        });
    });

    // Função para salvar o carrinho no localStorage
    function saveCart() {
        // JSON.stringify converte o array de objetos em uma string para poder ser armazenado
        localStorage.setItem('shoplinkCart', JSON.stringify(cart));
    }

    // Função para atualizar o contador 
    function updateCartCounter() {
        const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
        const counterElement = document.getElementById('cart-counter');
        if (counterElement) { // Verifica se o elemento existe na página
            counterElement.innerText = cartCount;
        }
    }

    // Chame esta função uma vez quando a página carregar
    updateCartCounter();
});