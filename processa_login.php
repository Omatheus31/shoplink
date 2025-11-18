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
        // A query SELECT * já busca a coluna 'role' que criamos
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            
            // SENHA CORRETA!
            session_regenerate_id(true);
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nome_loja'] = $usuario['nome_loja'];
            
            // --- MUDANÇA CRÍTICA AQUI ---
            // Agora salvamos o "cargo" do utilizador na sessão
            $_SESSION['role'] = $usuario['role'];
            // --- FIM DA MUDANÇA ---

            
            // Lógica de redirecionamento (que já fizemos)
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                $redirect_url = filter_var($_POST['redirect_url'], FILTER_SANITIZE_URL);
                if (strpos($redirect_url, 'login.php') === false && strpos($redirect_url, 'http') !== 0) {
                    header("Location: " . $redirect_url);
                    exit();
                }
            }
            
            // --- NOVA LÓGICA DE REDIRECIONAMENTO PADRÃO ---
            // Se não houver redirect_url, enviamos cada um para seu "home"
            if ($usuario['role'] === 'admin_master' || $usuario['role'] === 'admin_loja') {
                header("Location: admin/index.php"); // Admins vão para o Dashboard
            } else {
                header("Location: index.php"); // Clientes vão para o Catálogo
            }
            exit();
            // --- FIM DA NOVA LÓGICA ---

        } else {
            // SENHA ERRADA (lógica não muda)
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