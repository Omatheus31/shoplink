<?php
// salvar_pedido_logado.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}

header('Content-Type: application/json');
require_once 'config/database.php';

$id_usuario_logado = $_SESSION['id_usuario'];
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['carrinho']) || empty($dados['carrinho'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Carrinho vazio.']);
    exit;
}

// CAPTURA O MÉTODO DE PAGAMENTO (Padrão para PIX se falhar)
$metodo_pagamento = isset($dados['metodo_pagamento']) ? $dados['metodo_pagamento'] : 'PIX';

$carrinho = $dados['carrinho'];

try {
    $stmt_usuario = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt_usuario->execute([':id' => $id_usuario_logado]);
    $usuario = $stmt_usuario->fetch();

    if (!$usuario) throw new Exception("Usuário não encontrado.");
    
    $nome_cliente = $usuario['nome']; 
    $telefone_cliente = $usuario['telefone'];
    $endereco_formatado = $usuario['endereco_rua'] . ', ' . $usuario['endereco_numero'] . ' - ' . $usuario['endereco_bairro'];

    $total_pedido = 0;
    foreach ($carrinho as $item) {
        $total_pedido += $item['preco'] * $item['quantity'];
    }

    $pdo->beginTransaction();

    // INSERE PEDIDO COM O MÉTODO DE PAGAMENTO
    $sql_pedido = "INSERT INTO pedidos (id_usuario, nome_cliente, telefone_cliente, endereco_cliente, total_pedido, status, metodo_pagamento) 
                   VALUES (:id_usuario, :nome, :telefone, :endereco, :total, 'Aguardando Pagamento', :metodo)";
                   
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([
        ':id_usuario' => $id_usuario_logado,
        ':nome' => $nome_cliente,
        ':telefone' => $telefone_cliente,
        ':endereco' => $endereco_formatado,
        ':total' => $total_pedido,
        ':metodo' => $metodo_pagamento // Novo campo
    ]);
    
    $id_pedido = $pdo->lastInsertId();

    $sql_item = "INSERT INTO pedido_itens (id_pedido, id_produto, quantidade, preco_unitario) 
                 VALUES (:id_pedido, :id_produto, :quantidade, :preco)";
    $stmt_item = $pdo->prepare($sql_item);

    foreach ($carrinho as $item) {
        $stmt_item->execute([
            ':id_pedido' => $id_pedido,
            ':id_produto' => $item['id'],
            ':quantidade' => $item['quantity'],
            ':preco' => $item['preco']
        ]);
    }

    $pdo->commit();
    echo json_encode(['sucesso' => true, 'id_pedido' => $id_pedido]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
}
?>