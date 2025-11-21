<?php
require_once 'includes/header_admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clientes.php');
    exit();
}

$acao = $_POST['acao'] ?? '';
try {
    if ($acao === 'adicionar') {
        $nome = trim($_POST['nome_loja']);
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];
        $role = $_POST['role'] ?? 'cliente';

        // validações básicas
        if (empty($nome) || empty($email) || empty($senha)) {
            throw new Exception('Preencha todos os campos necessários.');
        }

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (nome_loja, email, senha_hash, role) VALUES (:nome, :email, :senha, :role)');
        $stmt->execute([':nome'=>$nome,':email'=>$email,':senha'=>$senha_hash,':role'=>$role]);
        header('Location: clientes.php?msg=criado');
        exit();
    }

    if ($acao === 'editar') {
        $id = (int)$_POST['id'];
        $nome = trim($_POST['nome_loja']);
        $email = trim($_POST['email']);
        $senha = $_POST['senha'] ?? '';
        $role = $_POST['role'] ?? 'cliente';

        if (empty($nome) || empty($email)) {
            throw new Exception('Nome e email são obrigatórios.');
        }

        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE usuarios SET nome_loja = :nome, email = :email, senha_hash = :senha, role = :role WHERE id = :id');
            $stmt->execute([':nome'=>$nome,':email'=>$email,':senha'=>$senha_hash,':role'=>$role,':id'=>$id]);
        } else {
            $stmt = $pdo->prepare('UPDATE usuarios SET nome_loja = :nome, email = :email, role = :role WHERE id = :id');
            $stmt->execute([':nome'=>$nome,':email'=>$email,':role'=>$role,':id'=>$id]);
        }

        header('Location: clientes.php?msg=editado');
        exit();
    }

} catch (Exception $e) {
    echo '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
}

require_once 'includes/footer_admin.php';
