<?php
// config/database.php

// Definições do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'shoplink_db');
define('DB_USER', 'root'); // Ou seu usuário do MySQL
define('DB_PASS', '');     // Ou sua senha do MySQL

// Configuração do DSN (Data Source Name)
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

// Opções do PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em caso de erro
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna resultados como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepares nativos do MySQL
];

try {
    // Cria a instância do PDO
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Em caso de erro na conexão, exibe uma mensagem e encerra o script
    // Em um ambiente de produção, seria melhor logar o erro do que exibi-lo.
    die('Erro de conexão com o banco de dados: ' . $e->getMessage());
}

// A variável $pdo já está pronta para ser usada em outros arquivos.
?>