<?php
// 1. INICIA A SESSÃO E VERIFICA O LOGIN
session_start();
if (!isset($_SESSION['id_usuario'])) {
    // Se não está logado, não pode salvar
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado. Faça login.']);
    exit;
}

header('Content-Type: application/json');
require_once 'config/database.php';

// Pega o ID do utilizador que está logado
$id_usuario_logado = $_SESSION['id_usuario'];

$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['carrinho']) || empty($dados['carrinho'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Carrinho vazio.']);
    exit;
}

$carrinho = $dados['carrinho'];

// 2. BUSCA OS DADOS DO UTILIZADOR LOGADO (NOME, ENDEREÇO) DO BANCO
try {
    $stmt_usuario = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt_usuario->execute([':id' => $id_usuario_logado]);
    $usuario = $stmt_usuario->fetch();

    if (!$usuario) {
        throw new Exception("Utilizador não encontrado.");
    }
    
    // Constrói o endereço
    $nome_cliente = $usuario['nome_loja']; // Reutilizando a coluna
    $telefone_cliente = $usuario['telefone'];
    $endereco_formatado = $usuario['endereco_rua'] . ', ' . $usuario['endereco_numero'] . ' - ' . $usuario['endereco_bairro'] . ', ' . $usuario['endereco_cidade'];

    // 3. CALCULA O TOTAL (NO BACKEND, MAIS SEGURO)
    $total_pedido = 0;
    foreach ($carrinho as $item) {
        // Futuramente, podemos verificar o preço real do produto no banco aqui
        $total_pedido += $item['preco'] * $item['quantity'];
    }

    $pdo->beginTransaction();

    // 4. INSERE O PEDIDO NA TABELA 'pedidos'
    // MUDANÇA: O status agora é 'Aguardando Pagamento'
    $sql_pedido = "INSERT INTO pedidos (id_usuario, nome_cliente, telefone_cliente, endereco_cliente, total_pedido, status) 
                   VALUES (:id_usuario, :nome, :telefone, :endereco, :total, 'Aguardando Pagamento')";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([
        ':id_usuario' => $id_usuario_logado,
        ':nome' => $nome_cliente,
        ':telefone' => $telefone_cliente,
        ':endereco' => $endereco_formatado,
        ':total' => $total_pedido
    ]);
    
    $id_pedido = $pdo->lastInsertId();

    // 5. INSERE OS ITENS NA TABELA 'pedido_itens'
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
    
    // Responde com sucesso, enviando o ID do novo pedido
    echo json_encode(['sucesso' => true, 'id_pedido' => $id_pedido]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar pedido: ' . $e->getMessage()]);
}
?>