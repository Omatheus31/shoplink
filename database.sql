CREATE DATABASE IF NOT EXISTS shoplink_db;
USE shoplink_db;

-- Tabela para guardar os produtos da loja
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    imagem_url VARCHAR(255) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela para configurações gerais da loja
CREATE TABLE configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(50) NOT NULL UNIQUE,
    valor VARCHAR(255) NOT NULL
);

-- Inserir a configuração inicial principal: o número de WhatsApp da loja
-- Coloque o número no formato internacional: código do país + DDD + número. Ex: 5593999999999
INSERT INTO configuracoes (chave, valor) VALUES ('whatsapp_numero', 'SEU_NUMERO_DE_WHATSAPP_AQUI');