<?php
// 1. INCLUI O HEADER DO ADMIN (Protege, conecta ao $pdo, nos dá $id_usuario_logado)
require_once 'includes/header_admin.php'; 

// 2. Verifica se é POST e se os dados vieram
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_pedido']) && isset($_POST['novo_status'])) {
        
        $id_pedido = (int)$_POST['id_pedido'];
        $novo_status = trim($_POST['novo_status']);

        try {
            // 3. ATUALIZA O STATUS DO PEDIDO (com lógica de 3 papéis)
            $sql = "UPDATE pedidos SET status = :novo_status 
                    WHERE id = :id_pedido";
            $params = [
                ':novo_status' => $novo_status,
                ':id_pedido' => $id_pedido
            ];

            // Admin Loja SÓ PODE atualizar o seu
            if ($_SESSION['role'] === 'admin_loja') {
                $sql .= " AND id_usuario = :id_usuario";
                $params[':id_usuario'] = $id_usuario_logado;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

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