<?php
// admin/verifica_login.php
session_start();

// 1. VERIFICA SE ESTÁ LOGADO
if (!isset($_SESSION['id_usuario'])) {
    $redirect_url = urlencode($_SERVER['REQUEST_URI']);
    header("Location: ../login.php?erro=acesso_negado&redirect_url=" . $redirect_url);
    exit();
}

// 2. VERIFICA O NÍVEL DE ACESSO (ROLE)
// No banco novo, o papel é apenas 'admin'
if ($_SESSION['role'] !== 'admin') {
    // Se for cliente tentando entrar no admin, manda pra home
    header("Location: ../index.php"); 
    exit();
}

// Se chegou aqui, é ADMIN liberado.
$id_usuario_logado = $_SESSION['id_usuario'];
// $nome_loja não é mais necessário na sessão para lógica, mas se quiser exibir no topo:
$nome_usuario_logado = $_SESSION['nome'] ?? 'Administrador';
?>