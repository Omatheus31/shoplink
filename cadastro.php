<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crie sua Conta - Shoplink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
    
    <div class="container my-5"> <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 fw-bold"><i class="bi bi-shop text-primary"></i> Crie sua Conta de Cliente</h1>
                            <p class="text-muted">Preencha seus dados para comprar.</p>
                        </div>

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
                            echo '<div class="alert alert-danger">' . $erro . '</div>';
                        }
                        ?>

                        <form action="processa_cadastro.php" method="POST">
                            <h5 class="mb-3 text-muted">1. Seus Dados</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nome_loja" name="nome_loja" placeholder="Nome Completo" required>
                                        <label for="nome_loja">Nome Completo</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                                        <label for="email">E-mail</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="Ex: 93912345678" required>
                                    <label for="telefone">WhatsApp / Telefone (com DDD)</label>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3 text-muted">2. Endereço de Entrega</h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="cep" name="endereco_cep" onblur="buscarCep()" maxlength="9" placeholder="CEP">
                                        <label for="cep">CEP</label>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="rua" name="endereco_rua" placeholder="Rua / Avenida" required>
                                        <label for="rua">Rua / Avenida</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="numero" name="endereco_numero" placeholder="Número" required>
                                        <label for="numero">Número</label>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="bairro" name="endereco_bairro" placeholder="Bairro" required>
                                        <label for="bairro">Bairro</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="cidade" name="endereco_cidade" placeholder="Cidade" required>
                                        <label for="cidade">Cidade</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="estado" name="endereco_estado" maxlength="2" placeholder="Estado (UF)" required>
                                        <label for="estado">Estado (UF)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="complemento" name="endereco_complemento" placeholder="Ex: Apto 101, Bloco B">
                                    <label for="complemento">Complemento (opcional)</label>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            <h5 class="mb-3 text-muted">3. Segurança</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="senha" name="senha" minlength="6" placeholder="Senha" required>
                                        <label for="senha">Crie uma Senha</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" placeholder="Confirme a Senha" required>
                                        <label for="confirma_senha">Confirme sua Senha</label>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-100 btn btn-lg btn-success">Criar minha conta</button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">Já tem uma conta?</p>
                            <a href="login.php" class="fw-bold text-decoration-none">Faça login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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