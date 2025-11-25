-- phpMyAdmin SQL Dump
-- version 5.2.1
-- Banco de dados: `shoplink_final`
-- Versão Limpa para Distribuição

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco_cep` varchar(10) DEFAULT NULL,
  `endereco_rua` varchar(255) DEFAULT NULL,
  `endereco_numero` varchar(20) DEFAULT NULL,
  `endereco_bairro` varchar(100) DEFAULT NULL,
  `endereco_cidade` varchar(100) DEFAULT NULL,
  `endereco_estado` varchar(50) DEFAULT NULL,
  `endereco_complemento` varchar(100) DEFAULT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `role` enum('admin','cliente') NOT NULL DEFAULT 'cliente',
  `data_cadastro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dados iniciais para `usuarios` (Senha: 123456)
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `telefone`, `senha_hash`, `role`) VALUES
(1, 'Administrador Shoplink', 'admin@shoplink.com', '93991337352', '$2y$10$a3cUsBb9Z1kpvc9kH1qQN.bp6YO5ZLmpPYV6WCCsT4PsZ3vAsmFD6', 'admin'),
(2, 'Cliente Exemplo', 'cliente@teste.com', '93991337352', '$2y$10$qS6TXkLde.Sszq4.XOlOIeLVNCmSw7.F0FnC2KornKZw60ai7p7si', 'cliente');

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dados iniciais para `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(16, 'Puff Banqueta'),
(15, 'Puff Baú');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `imagem_url` varchar(255) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dados iniciais para `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `id_categoria`, `imagem_url`) VALUES
(13, 'Puff Baú', 'Puff Baú\r\nMaterial: MDF\r\nRevestido com o tecido Suede\r\n\r\nTamanho:\r\nLargura: 45cm\r\nAltura: 40cm\r\nComprimento: 85cm\r\n\r\nSuporta até: 150kg', 150.00, 15, '69251217cbec2.png'),
(15, 'Puff Normal', 'Puff Normal Material: MDF \r\nRevestido com o tecido Suede \r\n\r\nLargura: 45cm \r\nAltura: 40cm \r\nComprimento: 85cm \r\n\r\nSuporta até: 150kg', 100.00, 16, '69251a91e4001.png'),
(17, 'Puff Baú Confort', 'Puff Baú: Conforto e Organização para Sua Casa!\r\n\r\nEstrutura: Madeira maciça.\r\nAssento: Espuma de poliuretano D26.\r\nRevestimento: Suede.\r\n\r\nMedidas: 85cm x 40cm x 45cm\r\nCarga máxima 100kg.', 250.00, 15, '6925a17b6f733.png'),
(18, 'Puff Baú Premium', 'Puff Baú Premium: Design, conforto e praticidade.\r\nIdeal para salas, quartos e escritórios.\r\n\r\nEstrutura: Madeira maciça.\r\nAssento: Espuma D26 e manta acrílica.\r\nAcabamento interno: TNT.\r\n\r\nMedidas: 1,35m x 40cm x 45cm\r\nCarga máxima 100kg', 500.00, 15, '6925a266369e6.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dados iniciais para `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`) VALUES
(1, 'whatsapp_numero', '5593991337352'),
(2, 'nome_loja', 'Mais Puff');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos` (Vazia para iniciar limpo)
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL COMMENT 'ID do Cliente',
  `nome_cliente` varchar(255) NOT NULL,
  `telefone_cliente` varchar(20) DEFAULT NULL,
  `endereco_cliente` text NOT NULL,
  `total_pedido` decimal(10,2) NOT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pendente',
  `metodo_pagamento` varchar(50) DEFAULT 'PIX',
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido_itens` (Vazia para iniciar limpo)
--

CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `id_produto` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_pedido` (`id_pedido`),
  KEY `id_produto` (`id_produto`),
  CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;