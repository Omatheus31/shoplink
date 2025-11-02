<?php
// 1. Inicia a sessão
session_start();

// 2. Destrói todas as variáveis da sessão (remove os dados do usuário)
$_SESSION = array();

// 3. Se desejar destruir a sessão completamente, também apague o cookie da sessão.
// Nota: Isso destruirá a sessão, e não apenas os dados da sessão!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finalmente, destrói a sessão
session_destroy();

// 5. Redireciona para a página de login
header("Location: login.php");
exit();
?>