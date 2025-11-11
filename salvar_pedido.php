<?php
header('Content-Type: application/json');
require_once 'config/database.php';

$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['cliente']) || !isset($dados['carrinho']) || empty($dados['carrinho'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados do pedido incompletos.']);
    exit;
}

$nome_cliente = $dados['cliente']['nome'];
$endereco_cliente = $dados['cliente']['endereco'];
$carrinho = $dados['carrinho'];

$total_pedido = 0;
foreach ($carrinho as $item) {
    $total_pedido += $item['preco'] * $item['quantity'];
}

// --- MUDANÇA CRÍTICA AQUI ---
// Define o ID do dono da loja (admin principal) para este pedido.
// Futuramente, este ID será dinâmico com base na URL da loja.
$id_loja_padrao = 1; 
// --- FIM DA MUDANÇA ---

$pdo->beginTransaction();

try {
    // 1. INSERIR O PEDIDO NA TABELA 'pedidos'
    // --- MUDANÇA NA QUERY SQL ---
    $sql_pedido = "INSERT INTO pedidos (nome_cliente, endereco_cliente, total_pedido, id_usuario) 
                   VALUES (:nome, :endereco, :total, :id_usuario)";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([
        ':nome' => $nome_cliente,
        ':endereco' => $endereco_cliente,
        ':total' => $total_pedido,
        ':id_usuario' => $id_loja_padrao // --- MUDANÇA AQUI ---
    ]);
    
    $id_pedido = $pdo->lastInsertId();

    // 2. INSERIR CADA ITEM (esta parte não muda)
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
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar pedido: ' . $e->getMessage()]);
}
?>