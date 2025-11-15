<?php
// 1. O Guardião: Protege a página
require_once 'verifica_login.php'; 
require_once '../config/database.php';

// 2. Verifica se é POST e se os dados vieram
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_pedido']) && isset($_POST['novo_status'])) {
        
        $id_pedido = (int)$_POST['id_pedido'];
        $novo_status = trim($_POST['novo_status']);

        try {
            // 3. ATUALIZA O STATUS DO PEDIDO
            // Garante que o admin só possa atualizar um pedido da sua própria loja
            $sql = "UPDATE pedidos SET status = :novo_status 
                    WHERE id = :id_pedido AND id_usuario = :id_usuario";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':novo_status' => $novo_status,
                ':id_pedido' => $id_pedido,
                ':id_usuario' => $id_usuario_logado
            ]);

            // 4. Redireciona de volta para a lista de pedidos com sucesso
            header("Location: pedidos.php?status_update=sucesso");
            exit();

        } catch (PDOException $e) {
            die("Erro ao atualizar status: " . $e->getMessage());
        }

    } else {
        // Dados do formulário não vieram
        header("Location: pedidos.php?erro=dados_invalidos");
        exit();
    }
} else {
    // Acesso não foi via POST
    header("Location: pedidos.php");
    exit();
}
?>