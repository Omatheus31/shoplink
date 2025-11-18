<?php
// 1. INICIA A SESSÃO
session_start();

// 2. VERIFICA SE ESTÁ LOGADO
if (!isset($_SESSION['id_usuario'])) {
    // Não está logado
    $redirect_url = urlencode($_SERVER['REQUEST_URI']);
    header("Location: ../login.php?erro=acesso_negado&redirect_url=" . $redirect_url);
    exit();
}

// 3. VERIFICA SE TEM O CARGO CORRETO
if ($_SESSION['role'] !== 'admin_master' && $_SESSION['role'] !== 'admin_loja') {
    // Está logado, mas é um 'cliente'
    // Chuta ele para fora do admin, enviando para o catálogo
    header("Location: ../index.php"); 
    exit();
}

// Se o script chegou até aqui, o utilizador está LOGADO e é um ADMIN.
$id_usuario_logado = $_SESSION['id_usuario'];
$nome_loja_logado = $_SESSION['nome_loja'];
?>