<?php
header('Content-Type: application/json');
require_once 'config/database.php';

$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['cliente']) || !isset($dados['carrinho']) || empty($dados['carrinho'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados do pedido incompletos.']);
    exit;
}

// Verifica se todas as chaves do cliente existem
$nome_cliente = $dados['cliente']['nome'] ?? 'Cliente';
$telefone_cliente = $dados['cliente']['telefone'] ?? 'N/A';
$endereco_cliente = $dados['cliente']['endereco'] ?? 'N/A';
$carrinho = $dados['carrinho'];

$total_pedido = 0;
foreach ($carrinho as $item) {
    $total_pedido += $item['preco'] * $item['quantity'];
}

$id_loja_padrao = 1; 

$pdo->beginTransaction();

try {
    $sql_pedido = "INSERT INTO pedidos (nome_cliente, telefone_cliente, endereco_cliente, total_pedido, id_usuario) 
                   VALUES (:nome, :telefone, :endereco, :total, :id_usuario)";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([
        ':nome' => $nome_cliente,
        ':telefone' => $telefone_cliente,
        ':endereco' => $endereco_cliente,
        ':total' => $total_pedido,
        ':id_usuario' => $id_loja_padrao
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
    // Retorna o nome do cliente para a mensagem de sucesso
    echo json_encode(['sucesso' => true, 'id_pedido' => $id_pedido, 'nome_cliente' => $nome_cliente]);

} catch (Exception $e) {
    $pdo->rollBack();
    // Garante que a mensagem de erro seja enviada
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar pedido: ' . $e->getMessage()]);
}
?>