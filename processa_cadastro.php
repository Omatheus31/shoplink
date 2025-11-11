<?php
// 1. INICIA A SESSÃO E CONECTA AO BANCO
session_start();
require_once 'config/database.php';

// 2. VERIFICA SE A REQUISIÇÃO É DO TIPO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. COLETA OS DADOS DO FORMULÁRIO
    $nome_loja = trim($_POST['nome_loja']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $confirma_senha = trim($_POST['confirma_senha']);

    // --- 4. VALIDAÇÃO DOS DADOS ---

    // Validação 1: Senhas não conferem
    if ($senha !== $confirma_senha) {
        header("Location: cadastro.php?erro=senhas_nao_conferem");
        exit();
    }

    // Validação 2: Campos vazios
    if (empty($nome_loja) || empty($email) || empty($senha)) {
        header("Location: cadastro.php?erro=campos_vazios");
        exit();
    }
    
    // (Opcional, mas recomendado) Validação de força da senha
    if (strlen($senha) < 6) {
        header("Location: cadastro.php?erro=senha_curta");
        exit();
    }

    // --- 5. VERIFICAR SE O E-MAIL JÁ EXISTE ---
    try {
        $sql_check = "SELECT id FROM usuarios WHERE email = :email";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([':email' => $email]);
        
        if ($stmt_check->fetch()) {
            // E-mail já existe
            header("Location: cadastro.php?erro=email_existe");
            exit();
        }

        // --- 6. TUDO CERTO! CRIAR O HASH DA SENHA ---
        $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

        if ($senha_hash === false) {
            die("Erro crítico ao gerar o hash da senha.");
        }

        // --- 7. INSERIR O NOVO USUÁRIO NO BANCO ---
        $sql_insert = "INSERT INTO usuarios (nome_loja, email, senha_hash) VALUES (:nome_loja, :email, :senha_hash)";
        $stmt_insert = $pdo->prepare($sql_insert);
        
        $stmt_insert->execute([
            ':nome_loja' => $nome_loja,
            ':email' => $email,
            ':senha_hash' => $senha_hash
        ]);

        // --- 8. LOGIN AUTOMÁTICO APÓS O CADASTRO ---
        // Pega o ID do usuário que acabamos de criar
        $id_novo_usuario = $pdo->lastInsertId();
        
        // Armazena os dados na sessão (logando o usuário)
        session_regenerate_id(true); // Segurança
        $_SESSION['id_usuario'] = $id_novo_usuario;
        $_SESSION['nome_loja'] = $nome_loja;

        // --- 9. REDIRECIONA PARA O DASHBOARD ---
        header("Location: admin/index.php"); // Leva o novo usuário direto para o painel
        exit();

    } catch (PDOException $e) {
        die("Erro no cadastro: " . $e->getMessage());
    }

} else {
    // Se alguém tentar acessar o arquivo diretamente
    header("Location: cadastro.php");
    exit();
}
?>