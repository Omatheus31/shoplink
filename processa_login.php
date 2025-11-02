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
            $_SESSION['nome_loja'] = $usuario['nome_loja'];
            
            // --- LÓGICA DE REDIRECIONAMENTO ATUALIZADA ---
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                // Filtra a URL para segurança básica
                $redirect_url = filter_var($_POST['redirect_url'], FILTER_SANITIZE_URL);
                
                // Evita redirecionar para sites externos ou de volta para o login
                if (strpos($redirect_url, 'login.php') === false && strpos($redirect_url, 'http') !== 0) {
                    header("Location: " . $redirect_url);
                    exit();
                }
            }
            
            // Se não houver redirect_url ou for inválido, vai para o padrão
            header("Location: admin/pedidos.php");
            exit();
            // --- FIM DA LÓGICA ATUALIZADA ---

        } else {
            // SENHA ERRADA - REDIRECIONAMENTO DE ERRO ATUALIZADO
            $redirect_query = '';
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                // Mantém a redirect_url na URL para a próxima tentativa
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