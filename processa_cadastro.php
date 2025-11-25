<?php
// processa_cadastro.php
session_start();
require_once 'config/database.php';
require_once 'includes/email.php'; // Inclui o disparador de e-mail

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Recebe e limpa os dados
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $confirmar_email = trim($_POST['confirmar_email']); // Novo campo
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

    // 2. VALIDAÇÕES BÁSICAS
    
    // Valida E-mails iguais
    if ($email !== $confirmar_email) {
        header("Location: cadastro.php?erro=emails_nao_conferem"); // Você pode criar esse erro no cadastro.php se quiser
        exit();
    }

    // Valida Senhas iguais
    if ($senha !== $confirma_senha) {
        header("Location: cadastro.php?erro=senhas_nao_conferem");
        exit();
    }

    // Valida tamanho da senha
    if (strlen($senha) < 8) { // Ajustei para 8 conforme seu checklist visual
        header("Location: cadastro.php?erro=senha_curta");
        exit();
    }

    try {
        // 3. Verifica se email já existe no banco
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->rowCount() > 0) {
            header("Location: cadastro.php?erro=email_existe");
            exit();
        }

        // 4. Hash da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // 5. Insere no banco
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

        // 6. ENVIA E-MAIL DE BOAS-VINDAS (Opcional, mas chique!)
        $corpo_email = "
            <p>Olá, <strong>$nome</strong>.</p>
            <p>Seu cadastro foi realizado com sucesso. Agora você pode aproveitar nossas ofertas exclusivas.</p>
            <p>Seus dados de acesso:</p>
            <ul>
                <li><strong>E-mail:</strong> $email</li>
                <li><strong>Senha:</strong> (Protegida)</li>
            </ul>
            <p><a href='http://localhost/shoplink/login.php'>Clique aqui para acessar sua conta</a></p>
        ";
        // Tenta enviar sem travar o processo se falhar
        enviarEmail($email, $nome, 'Bem-vindo ao Shoplink!', $corpo_email);

        // 7. Login Automático
        $id_novo = $pdo->lastInsertId();
        $_SESSION['id_usuario'] = $id_novo;
        $_SESSION['nome'] = $nome;
        $_SESSION['role'] = 'cliente';

        // Redireciona para a Home com aviso
        header("Location: index.php?msg=bem_vindo");
        exit();

    } catch (PDOException $e) {
        die("Erro no banco de dados: " . $e->getMessage());
    }

} else {
    header("Location: cadastro.php");
    exit();
}
?>