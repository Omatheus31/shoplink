<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (empty($email) || empty($senha)) {
        header("Location: login.php?erro=credenciais");
        exit();
    }

    try {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            
            // SENHA CORRETA!
            session_regenerate_id(true);
            $_SESSION['id_usuario'] = $usuario['id'];
            
            // ATENÇÃO: Mudamos de 'nome_loja' para 'nome' conforme o novo banco
            $_SESSION['nome'] = $usuario['nome']; 
            
            // Salva o cargo (role). Agora só existem 'admin' ou 'cliente'
            $_SESSION['role'] = $usuario['role'];

            
            // 1. Verifica se existe URL de redirecionamento forçado (ex: veio do carrinho)
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                $redirect_url = filter_var($_POST['redirect_url'], FILTER_SANITIZE_URL);
                // Segurança básica para evitar redirecionamento externo malicioso
                if (strpos($redirect_url, 'login.php') === false && strpos($redirect_url, 'http') !== 0) {
                    header("Location: " . $redirect_url);
                    exit();
                }
            }
            
            // 2. Redirecionamento Padrão (Baseado no cargo simples)
            if ($usuario['role'] === 'admin') {
                header("Location: admin/index.php"); // Admin vai para o Painel
            } else {
                header("Location: index.php"); // Cliente vai para a Loja
            }
            exit();

        } else {
            // SENHA ERRADA
            $redirect_query = '';
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                $redirect_query = '&redirect_url=' . urlencode($_POST['redirect_url']);
            }
            header("Location: login.php?erro=credenciais" . $redirect_query);
            exit();
        }

    } catch (PDOException $e) {
        die("Erro no login: " . $e->getMessage());
    }

} else {
    header("Location: login.php");
    exit();
}
?>