<?php
// Define que o conteúdo será um JSON
header('Content-Type: application/json');

// Inclui a conexão com o banco de dados
require_once 'config/database.php';

// Pega os dados brutos enviados via POST
// (Como estamos enviando JSON via fetch, não podemos usar $_POST)
$dados = json_decode(file_get_contents('php://input'), true);

// Validação básica dos dados recebidos
if (!isset($dados['cliente']) || !isset($dados['carrinho']) || empty($dados['carrinho'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados do pedido incompletos.']);
    exit;
}

// Dados do cliente
$nome_cliente = $dados['cliente']['nome'];
$endereco_cliente = $dados['cliente']['endereco'];

// Dados do carrinho
$carrinho = $dados['carrinho'];

// Calcular o total do pedido (é mais seguro recalcular no backend)
$total_pedido = 0;
foreach ($carrinho as $item) {
    // Aqui poderíamos verificar o preço real no banco de dados por segurança,
    // mas por enquanto vamos confiar no preço vindo do cliente.
    $total_pedido += $item['preco'] * $item['quantity'];
}

// Inicia uma transação para garantir que tudo seja salvo ou nada seja salvo
$pdo->beginTransaction();

try {
    // 1. INSERIR O PEDIDO NA TABELA 'pedidos'
    $sql_pedido = "INSERT INTO pedidos (nome_cliente, endereco_cliente, total_pedido) VALUES (:nome, :endereco, :total)";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([
        ':nome' => $nome_cliente,
        ':endereco' => $endereco_cliente,
        ':total' => $total_pedido
    ]);
    
    // Pega o ID do pedido que acabamos de criar
    $id_pedido = $pdo->lastInsertId();

    // 2. INSERIR CADA ITEM DO CARRINHO NA TABELA 'pedido_itens'
    $sql_item = "INSERT INTO pedido_itens (id_pedido, id_produto, quantidade, preco_unitario) VALUES (:id_pedido, :id_produto, :quantidade, :preco)";
    $stmt_item = $pdo->prepare($sql_item);

    foreach ($carrinho as $item) {
        $stmt_item->execute([
            ':id_pedido' => $id_pedido,
            ':id_produto' => $item['id'],
            ':quantidade' => $item['quantity'],
            ':preco' => $item['preco']
        ]);
    }

    // Se tudo deu certo até aqui, confirma a transação
    $pdo->commit();
    
    // Retorna uma resposta de sucesso com o ID do novo pedido
    echo json_encode(['sucesso' => true, 'id_pedido' => $id_pedido]);

} catch (Exception $e) {
    // Se algo deu errado, desfaz a transação
    $pdo->rollBack();
    
    // Retorna uma resposta de erro
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar pedido: ' . $e->getMessage()]);
}
?>