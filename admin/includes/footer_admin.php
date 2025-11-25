<footer class="d-flex justify-content-center align-items-center py-4 my-4 border-top">
        <p class="mb-0 text-muted small">
            &copy; <?php echo date('Y'); ?> Shoplink - Painel de Administração
        </p>
    </footer>
    </main> </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Encontra todos os inputs do tipo password
        const passwordInputs = document.querySelectorAll('input[type="password"]');

        passwordInputs.forEach(function(input) {
            // 2. Cria o wrapper (div) do Bootstrap se não existir
            // Apenas se o input já não estiver dentro de um input-group com botão
            if (!input.parentElement.classList.contains('input-group')) {
                
                const wrapper = document.createElement('div');
                wrapper.className = 'input-group';
                
                // Insere o wrapper antes do input e move o input pra dentro
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);

                // 3. Cria o botão do olho
                const button = document.createElement('button');
                button.className = 'btn btn-outline-secondary';
                button.type = 'button';
                button.innerHTML = '<i class="bi bi-eye"></i>';
                button.style.zIndex = "10"; // Garante que fique clicável

                // 4. Adiciona evento de clique
                button.addEventListener('click', function() {
                    if (input.type === "password") {
                        input.type = "text";
                        button.innerHTML = '<i class="bi bi-eye-slash"></i>';
                    } else {
                        input.type = "password";
                        button.innerHTML = '<i class="bi bi-eye"></i>';
                    }
                });

                // Adiciona o botão ao grupo
                wrapper.appendChild(button);
            }
        });
    });
    </script>
</body>
</html>


