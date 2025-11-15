<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    header("Location: admin/index.php"); // Se já logado, manda pro admin
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crie sua Conta - Shoplink</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 40px 0; /* Espaço para rolagem */
        }
        .login-container {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px; /* Um pouco mais largo */
            text-align: center;
        }
        .login-container h1 { margin-top: 0; color: #2c3e50; }
        .login-form-group { margin-bottom: 1.2rem; text-align: left; }
        .login-form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        .login-form-group input {
            width: 95%; /* Ajustado para padding */
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .login-btn {
            width: 100%;
            padding: 0.85rem;
            border: none;
            border-radius: 4px;
            background-color: #27ae60;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-btn:hover { background-color: #229954; }
        .login-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .login-footer { margin-top: 1.5rem; color: #555; }
        .login-footer a { color: #3498db; font-weight: 600; text-decoration: none; }
        
        /* Estilo para campos lado a lado */
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .login-form-group {
            flex: 1; /* Ocupa espaço igual */
        }
        /* Ajusta a largura do input dentro do form-row */
        .form-row .login-form-group input {
            width: 90%; 
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Crie sua Conta de Cliente</h1>
        <p>Preencha seus dados para comprar.</p>

        <?php 
        if (isset($_GET['erro'])) {
            $erro = '';
            if ($_GET['erro'] === 'email_existe') {
                $erro = 'Este e-mail já está cadastrado. Tente outro.';
            } elseif ($_GET['erro'] === 'senhas_nao_conferem') {
                $erro = 'As senhas não conferem.';
            } elseif ($_GET['erro'] === 'senha_curta') {
                $erro = 'Sua senha deve ter no mínimo 6 caracteres.';
            } else {
                $erro = 'Ocorreu um erro. Tente novamente.';
            }
            echo '<div class="login-error">' . $erro . '</div>';
        }
        ?>

        <form action="processa_cadastro.php" method="POST">
            <div class="login-form-group">
                <label for="nome_loja">Seu Nome Completo:</label>
                <input type="text" id="nome_loja" name="nome_loja" required>
            </div>
            <div class="login-form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="login-form-group">
                <label for="telefone">WhatsApp / Telefone (com DDD):</label>
                <input type="tel" id="telefone" name="telefone" placeholder="Ex: 93912345678" required>
            </div>
            
            <hr style="border:0; border-top:1px solid #eee; margin: 25px 0;">
            
            <div class="login-form-group">
                <label for="cep">CEP:</label>
                <input type="text" id="cep" name="endereco_cep" onblur="buscarCep()" maxlength="9" placeholder="Apenas números">
            </div>
            <div class="login-form-group">
                <label for="rua">Rua / Avenida:</label>
                <input type="text" id="rua" name="endereco_rua" required>
            </div>
            <div class="form-row">
                <div class="login-form-group">
                    <label for="numero">Número:</label>
                    <input type="text" id="numero" name="endereco_numero" required>
                </div>
                <div class="login-form-group">
                    <label for="bairro">Bairro:</label>
                    <input type="text" id="bairro" name="endereco_bairro" required>
                </div>
            </div>
            <div class="login-form-group">
                <label for="cidade">Cidade:</label>
                <input type="text" id="cidade" name="endereco_cidade" required>
            </div>
            <div class="login-form-group">
                <label for="estado">Estado (UF):</label>
                <input type="text" id="estado" name="endereco_estado" maxlength="2" required>
            </div>
            <div class="login-form-group">
                <label for="complemento">Complemento (opcional):</label>
                <input type="text" id="complemento" name="endereco_complemento" placeholder="Ex: Apto 101, Bloco B">
            </div>

            <hr style="border:0; border-top:1px solid #eee; margin: 25px 0;">

            <div class="login-form-group">
                <label for="senha">Crie uma Senha:</label>
                <input type="password" id="senha" name="senha" minlength="6" required>
            </div>
            <div class="login-form-group">
                <label for="confirma_senha">Confirme sua Senha:</label>
                <input type="password" id="confirma_senha" name="confirma_senha" required>
            </div>
            <button type="submit" class="login-btn">Criar minha conta</button>
        </form>

        <div class="login-footer">
            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
        </div>
    </div>

    <script>
        function buscarCep() {
            const cepInput = document.getElementById('cep');
            let cep = cepInput.value.replace(/\D/g, ''); 

            if (cep.length === 8) {
                cepInput.value = cep.substring(0, 5) + '-' + cep.substring(5);
                setAddressFieldsReadOnly(true);

                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('rua').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                            document.getElementById('numero').focus();
                        } else {
                            alert('CEP não encontrado.');
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error))
                    .finally(() => {
                        setAddressFieldsReadOnly(false);
                    });
            }
        }

        function setAddressFieldsReadOnly(isReadOnly) {
            document.getElementById('rua').readOnly = isReadOnly;
            document.getElementById('bairro').readOnly = isReadOnly;
            document.getElementById('cidade').readOnly = isReadOnly;
            document.getElementById('estado').readOnly = isReadOnly;
        }
    </script>
</body>
</html>