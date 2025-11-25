-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/11/2025 às 13:45
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `shoplink_final`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(16, 'Puff Banqueta'),
(15, 'Puff Baú');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`) VALUES
(1, 'whatsapp_numero', '5593991337352'),
(2, 'nome_loja', 'Mais Puff');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL COMMENT 'ID do Cliente',
  `nome_cliente` varchar(255) NOT NULL,
  `telefone_cliente` varchar(20) DEFAULT NULL,
  `endereco_cliente` text NOT NULL,
  `total_pedido` decimal(10,2) NOT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pendente',
  `metodo_pagamento` varchar(50) DEFAULT 'PIX'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_usuario`, `nome_cliente`, `telefone_cliente`, `endereco_cliente`, `total_pedido`, `data_pedido`, `status`, `metodo_pagamento`) VALUES
(6, 1, 'Administrador Shoplink', '93991337352', ',  - ', 300.00, '2025-11-25 03:06:05', 'Pago', 'PIX'),
(7, 2, 'Cliente Exemplo', '93991337352', ',  - ', 300.00, '2025-11-25 11:49:37', 'Aguardando Pagamento', 'PIX'),
(8, NULL, 'Pablo', '93984154861', 'Avenida Papagaio, 11479 - Salvação', 100.00, '2025-11-25 11:53:32', 'Aguardando Pagamento', 'Cartão de Crédito'),
(9, NULL, 'Pablo', '93984154861', 'Avenida Papagaio, 11479 - Salvação', 100.00, '2025-11-25 11:57:58', 'Aguardando Pagamento', 'Boleto'),
(10, NULL, 'Pablo', '93984154861', 'Avenida Papagaio, 11479 - Salvação', 100.00, '2025-11-25 11:58:29', 'Aguardando Pagamento', 'PIX');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_produto` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `id_pedido`, `id_produto`, `quantidade`, `preco_unitario`) VALUES
(9, 6, 13, 2, 150.00),
(10, 7, 13, 2, 150.00),
(11, 8, 15, 1, 100.00),
(12, 9, 15, 1, 100.00),
(13, 10, 15, 1, 100.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `imagem_url` varchar(255) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `id_categoria`, `imagem_url`, `data_criacao`) VALUES
(13, 'Puff Baú', 'Puff Baú\r\nMaterial: MDF\r\nRevestido com o tecido Suede\r\n\r\nTamanho:\r\nLargura: 45cm\r\nAltura: 40cm\r\nComprimento: 85cm\r\n\r\nSuporta até: 150kg', 150.00, 15, '69251217cbec2.png', '2025-11-25 02:19:03'),
(15, 'Puff', 'Puff Normal Material: MDF \r\nRevestido com o tecido Suede \r\n\r\nLargura: 45cm \r\nAltura: 40cm \r\nComprimento: 85cm \r\n\r\nSuporta até: 150kg', 100.00, 16, '69251a91e4001.png', '2025-11-25 02:55:13'),
(17, 'Puff Baú', 'Puff Baú: Conforto e Organização para Sua Casa!\r\n\r\nEstrutura: Madeira maciça (como eucalipto) para maior durabilidade.\r\nAssento: Espuma de poliuretano (densidade D26 ou D14) e manta acrílica, garantindo conforto e qualidade.\r\nRevestimento: Suede, couro sintético ou polipropileno, com toque suave e resistente.\r\nAcabamento interno: TNT, com alças que evitam que a tampa caia.\r\nPés: Maciços e reforçados para maior estabilidade e suporte de peso. \r\n\r\nMedidas: \r\n  Comprimento - 85cm\r\n  Largura - 40cm\r\n  Altura - 45cm\r\n\r\nPeso suportado: Carga máxima 100kg).', 250.00, 15, '6925a17b6f733.png', '2025-11-25 12:30:19'),
(18, 'Puff Baú', 'Puff Baú Premium: Design, conforto e praticidade em uma só peça. \r\n\r\nIdeal para salas, quartos, closets e escritórios, ele se adapta a diferentes necessidades e decorações.\r\n\r\nEstrutura: Madeira maciça (como eucalipto) para maior durabilidade.\r\nAssento: Espuma de poliuretano (densidade D26 ou D14) e manta acrílica, garantindo conforto e qualidade.\r\nRevestimento: Suede, couro sintético ou polipropileno, com toque suave e resistente.\r\nAcabamento interno: TNT, com alças que evitam que a tampa caia.\r\nPés: Maciços e reforçados para maior estabilidade e suporte de peso. \r\n\r\nMedidas: \r\n  Comprimento - 1,35m\r\n  Largura - 40cm\r\n  Altura - 45cm\r\n\r\nCarga máxima 100kg', 500.00, 15, '6925a266369e6.png', '2025-11-25 12:34:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
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
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `telefone`, `endereco_cep`, `endereco_rua`, `endereco_numero`, `endereco_bairro`, `endereco_cidade`, `endereco_estado`, `endereco_complemento`, `senha_hash`, `role`, `data_cadastro`) VALUES
(1, 'Administrador Shoplink', 'admin@shoplink.com', '93991337352', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$a3cUsBb9Z1kpvc9kH1qQN.bp6YO5ZLmpPYV6WCCsT4PsZ3vAsmFD6', 'admin', '2025-11-20 20:18:50'),
(2, 'Cliente Exemplo', 'cliente@teste.com', '93991337352', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$qS6TXkLde.Sszq4.XOlOIeLVNCmSw7.F0FnC2KornKZw60ai7p7si', 'cliente', '2025-11-20 20:18:50');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
