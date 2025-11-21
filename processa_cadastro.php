<?php
// processa_cadastro.php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Recebe e limpa os dados
    $nome = trim($_POST['nome']); // CORRIGIDO: era nome_loja
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    
    // Endereço
    $cep = trim($_POST['endereco_cep'] ?? '');
    $rua = trim($_POST['endereco_rua'] ?? '');
    $numero = trim($_POST['endereco_numero'] ?? '');
    $bairro = trim($_POST['endereco_bairro'] ?? '');
    $cidade = trim($_POST['endereco_cidade'] ?? '');
    $estado = trim($_POST['endereco_estado'] ?? '');
    $complemento = trim($_POST['endereco_complemento'] ?? '');

    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];

    // 2. Validações básicas
    if ($senha !== $confirma_senha) {
        header("Location: cadastro.php?erro=senhas_nao_conferem");
        exit();
    }

    if (strlen($senha) < 6) {
        header("Location: cadastro.php?erro=senha_curta");
        exit();
    }

    try {
        // 3. Verifica se email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->rowCount() > 0) {
            header("Location: cadastro.php?erro=email_existe");
            exit();
        }

        // 4. Hash da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // 5. Insere no banco (COM A NOVA ESTRUTURA DE COLUNAS)
        $sql = "INSERT INTO usuarios (nome, email, telefone, endereco_cep, endereco_rua, endereco_numero, endereco_bairro, endereco_cidade, endereco_estado, endereco_complemento, senha_hash, role) 
                VALUES (:nome, :email, :telefone, :cep, :rua, :numero, :bairro, :cidade, :estado, :complemento, :senha_hash, 'cliente')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':cep' => $cep,
            ':rua' => $rua,
            ':numero' => $numero,
            ':bairro' => $bairro,
            ':cidade' => $cidade,
            ':estado' => $estado,
            ':complemento' => $complemento,
            ':senha_hash' => $senha_hash
        ]);

        // 6. Sucesso - Redireciona para login
        header("Location: login.php?cadastro=sucesso");
        exit();

    } catch (PDOException $e) {
        // Em produção, logar o erro e não exibir na tela
        die("Erro no banco de dados: " . $e->getMessage());
    }

} else {
    header("Location: cadastro.php");
    exit();
}
?>