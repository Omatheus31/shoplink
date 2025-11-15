<?php
// 1. INICIA A SESSÃO E CONECTA AO BANCO
session_start();
require_once 'config/database.php';

// 2. VERIFICA SE A REQUISIÇÃO É DO TIPO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. COLETA DOS DADOS BÁSICOS
    $nome_loja = trim($_POST['nome_loja']); // Nome do Cliente
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $senha = trim($_POST['senha']);
    $confirma_senha = trim($_POST['confirma_senha']);

    // 4. COLETA DOS DADOS DE ENDEREÇO
    $cep = trim($_POST['endereco_cep']);
    $rua = trim($_POST['endereco_rua']);
    $numero = trim($_POST['endereco_numero']);
    $bairro = trim($_POST['endereco_bairro']);
    $cidade = trim($_POST['endereco_cidade']);
    $estado = trim($_POST['endereco_estado']);
    $complemento = trim($_POST['endereco_complemento']);

    // --- 5. VALIDAÇÃO DOS DADOS ---

    if ($senha !== $confirma_senha) {
        header("Location: cadastro.php?erro=senhas_nao_conferem");
        exit();
    }
    if (empty($nome_loja) || empty($email) || empty($telefone) || empty($senha)) {
        header("Location: cadastro.php?erro=campos_vazios");
        exit();
    }
    if (strlen($senha) < 6) {
        header("Location: cadastro.php?erro=senha_curta");
        exit();
    }

    // --- 6. VERIFICAR SE O E-MAIL JÁ EXISTE ---
    try {
        $sql_check = "SELECT id FROM usuarios WHERE email = :email";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([':email' => $email]);
        
        if ($stmt_check->fetch()) {
            header("Location: cadastro.php?erro=email_existe");
            exit();
        }

        // --- 7. TUDO CERTO! CRIAR O HASH DA SENHA ---
        $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
        if ($senha_hash === false) {
            die("Erro crítico ao gerar o hash da senha.");
        }

        // --- 8. INSERIR O NOVO USUÁRIO (AGORA COM ENDEREÇO) ---
        // A coluna 'role' usará o valor DEFAULT 'cliente' que definimos no banco
        $sql_insert = "INSERT INTO usuarios (
                            nome_loja, email, telefone, 
                            endereco_cep, endereco_rua, endereco_numero, 
                            endereco_bairro, endereco_cidade, endereco_estado, endereco_complemento, 
                            senha_hash
                       ) VALUES (
                            :nome_loja, :email, :telefone, 
                            :cep, :rua, :numero, 
                            :bairro, :cidade, :estado, :complemento, 
                            :senha_hash
                       )";
        
        $stmt_insert = $pdo->prepare($sql_insert);
        
        $stmt_insert->execute([
            ':nome_loja' => $nome_loja,
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

        // --- 9. LOGIN AUTOMÁTICO APÓS O CADASTRO ---
        $id_novo_usuario = $pdo->lastInsertId();
        
        session_regenerate_id(true);
        $_SESSION['id_usuario'] = $id_novo_usuario;
        $_SESSION['nome_loja'] = $nome_loja;
        $_SESSION['role'] = 'cliente'; // Define o papel na sessão

        // --- 10. REDIRECIONA PARA O CATÁLOGO ---
        header("Location: index.php"); 
        exit();

    } catch (PDOException $e) {
        die("Erro no cadastro: " . $e->getMessage());
    }

} else {
    header("Location: cadastro.php");
    exit();
}
?>