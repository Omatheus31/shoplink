<?php
// 1. INICIA A SESSÃO
session_start();

// 2. VERIFICA SE A SESSÃO DO USUÁRIO NÃO EXISTE
if (!isset($_SESSION['id_usuario'])) {
    
    // --- MUDANÇA AQUI ---
    // Captura a URL que o usuário estava tentando acessar (ex: /shoplink/admin/categorias.php)
    $redirect_url = urlencode($_SERVER['REQUEST_URI']);
    
    // Redireciona para o login, passando a URL de destino como um parâmetro
    header("Location: ../login.php?erro=acesso_negado&redirect_url=" . $redirect_url);
    exit();
    // --- FIM DA MUDANÇA ---
}

// Se o script chegou até aqui, o usuário está logado.
$id_usuario_logado = $_SESSION['id_usuario'];
$nome_loja_logado = $_SESSION['nome_loja'];
?>