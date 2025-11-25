<?php
// salvar_pedido_logado.php
session_start();

// 1. VERIFICA O LOGIN
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado. Faça login.']);
    exit;
}

header('Content-Type: application/json');
require_once 'config/database.php';
require_once 'includes/email.php'; // <--- IMPORTANTE: Inclui o disparador

$id_usuario_logado = $_SESSION['id_usuario'];

// Recebe o JSON do JavaScript
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['carrinho']) || empty($dados['carrinho'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Carrinho vazio.']);
    exit;
}

$carrinho = $dados['carrinho'];
$metodo_pagamento = isset($dados['metodo_pagamento']) ? $dados['metodo_pagamento'] : 'PIX';

try {
    // 2. BUSCA DADOS DO CLIENTE
    $stmt_usuario = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt_usuario->execute([':id' => $id_usuario_logado]);
    $usuario = $stmt_usuario->fetch();

    if (!$usuario) {
        throw new Exception("Usuário não encontrado.");
    }
    
    $nome_cliente = $usuario['nome']; 
    $email_cliente = $usuario['email']; // Precisamos do email para enviar o recibo
    $telefone_cliente = $usuario['telefone'];
    
    // Formata endereço
    $rua = $usuario['endereco_rua'] ?? '';
    $numero = $usuario['endereco_numero'] ?? '';
    $bairro = $usuario['endereco_bairro'] ?? '';
    $cidade = $usuario['endereco_cidade'] ?? '';
    $endereco_formatado = "$rua, $numero - $bairro, $cidade";

    // 3. CALCULA O TOTAL E PREPARA LISTA PARA O EMAIL
    $total_pedido = 0;
    $itens_email_html = ""; // String para guardar o HTML da lista

    foreach ($carrinho as $item) {
        $subtotal = $item['preco'] * $item['quantity'];
        $total_pedido += $subtotal;
        
        // Monta linha da tabela do email
        $itens_email_html .= "
            <tr>
                <td style='padding: 5px; border-bottom: 1px solid #ddd;'>{$item['nome']}</td>
                <td style='padding: 5px; border-bottom: 1px solid #ddd;'>{$item['quantity']}</td>
                <td style='padding: 5px; border-bottom: 1px solid #ddd;'>R$ " . number_format($item['preco'], 2, ',', '.') . "</td>
            </tr>
        ";
    }

    // Inicia Transação
    $pdo->beginTransaction();

    // 4. INSERE O PEDIDO
    $sql_pedido = "INSERT INTO pedidos (id_usuario, nome_cliente, telefone_cliente, endereco_cliente, total_pedido, status, metodo_pagamento) 
                   VALUES (:id_usuario, :nome, :telefone, :endereco, :total, 'Aguardando Pagamento', :metodo)";
                   
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([
        ':id_usuario' => $id_usuario_logado,
        ':nome' => $nome_cliente,
        ':telefone' => $telefone_cliente,
        ':endereco' => $endereco_formatado,
        ':total' => $total_pedido,
        ':metodo' => $metodo_pagamento
    ]);
    
    $id_pedido = $pdo->lastInsertId();

    // 5. INSERE OS ITENS NO BANCO
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

    // 6. ENVIA O E-MAIL DE CONFIRMAÇÃO (Tenta enviar, mas não trava se falhar)
    // Só executa se estivermos em localhost ou se o servidor permitir
    
    $assunto = "Pedido #$id_pedido Recebido - Shoplink";
    $corpo_email = "
        <div style='font-family: Arial, sans-serif; color: #333;'>
            <h2 style='color: #0d6efd;'>Obrigado pela sua compra!</h2>
            <p>Olá, <strong>$nome_cliente</strong>. Seu pedido foi recebido com sucesso.</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <p><strong>Pedido:</strong> #$id_pedido</p>
                <p><strong>Data:</strong> " . date('d/m/Y H:i') . "</p>
                <p><strong>Pagamento:</strong> $metodo_pagamento</p>
                <p><strong>Total:</strong> <span style='color: green; font-weight: bold;'>R$ " . number_format($total_pedido, 2, ',', '.') . "</span></p>
            </div>

            <h3>Itens do Pedido:</h3>
            <table style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr style='background: #eee;'>
                        <th style='text-align: left; padding: 5px;'>Produto</th>
                        <th style='text-align: left; padding: 5px;'>Qtd</th>
                        <th style='text-align: left; padding: 5px;'>Preço</th>
                    </tr>
                </thead>
                <tbody>
                    $itens_email_html
                </tbody>
            </table>
            
            <p style='margin-top: 20px;'>Você pode acompanhar o status em 'Meus Pedidos' no site.</p>
            <hr>
            <small style='color: #999;'>Shoplink - E-commerce</small>
        </div>
    ";

    // Envia o e-mail (Função do includes/email.php)
    // Nota: Se estiver na InfinityFree, isso pode não chegar, mas não vai gerar erro pro usuário
    enviarEmail($email_cliente, $nome_cliente, $assunto, $corpo_email);
    
    // 7. RETORNA SUCESSO
    echo json_encode(['sucesso' => true, 'id_pedido' => $id_pedido]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao processar: ' . $e->getMessage()]);
}
?>