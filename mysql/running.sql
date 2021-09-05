-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 07-Fev-2021 às 01:38
-- Versão do servidor: 8.0.23-0ubuntu0.20.04.1
-- versão do PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `running`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `compra`
--

CREATE TABLE `compra` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `total` float DEFAULT NULL,
  `data_comp` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `compra`
--

INSERT INTO `compra` (`id`, `id_usuario`, `total`, `data_comp`) VALUES
(1, 4, 150, '2020-11-05');

-- --------------------------------------------------------

--
-- Estrutura da tabela `debito`
--

CREATE TABLE `debito` (
  `id` int NOT NULL,
  `id_pag` int DEFAULT NULL,
  `id_comp` int NOT NULL,
  `vencimento` date DEFAULT NULL,
  `parcela` int DEFAULT NULL,
  `valor` float DEFAULT NULL,
  `valor_pag` float DEFAULT NULL,
  `data_pag` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `debito`
--

INSERT INTO `debito` (`id`, `id_pag`, `id_comp`, `vencimento`, `parcela`, `valor`, `valor_pag`, `data_pag`) VALUES
(1, 1, 1, '2020-11-04', 2, 75, 75, '2020-11-05');

-- --------------------------------------------------------

--
-- Estrutura da tabela `exercicio`
--

CREATE TABLE `exercicio` (
  `id` int NOT NULL,
  `nome` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `exercicio`
--

INSERT INTO `exercicio` (`id`, `nome`) VALUES
(1, 'Remada alta no cross'),
(2, 'Crucifixo no cross');

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens`
--

CREATE TABLE `itens` (
  `id_comp` int NOT NULL,
  `id_prodserv` int NOT NULL,
  `quantidade` int DEFAULT NULL,
  `valor` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `itens`
--

INSERT INTO `itens` (`id_comp`, `id_prodserv`, `quantidade`, `valor`) VALUES
(1, 1, 1, 75),
(1, 2, 1, 75);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamento`
--

CREATE TABLE `pagamento` (
  `id` int NOT NULL,
  `data_pag` date DEFAULT NULL,
  `total_val` float DEFAULT NULL,
  `total_pag` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `pagamento`
--

INSERT INTO `pagamento` (`id`, `data_pag`, `total_val`, `total_pag`) VALUES
(1, '2020-11-05', 150, 75);

-- --------------------------------------------------------

--
-- Estrutura da tabela `prodserv`
--

CREATE TABLE `prodserv` (
  `id` int NOT NULL,
  `nome` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `valor` float DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `prodserv`
--

INSERT INTO `prodserv` (`id`, `nome`, `valor`, `tipo`) VALUES
(1, 'Calsa', 100, 'p'),
(2, 'Musculação', 70, 's'),
(3, 'Casaco', 150, 'p');

-- --------------------------------------------------------

--
-- Estrutura da tabela `treino`
--

CREATE TABLE `treino` (
  `id` int NOT NULL,
  `id_usuario` int DEFAULT NULL,
  `nome` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `treino`
--

INSERT INTO `treino` (`id`, `id_usuario`, `nome`, `data_inicio`, `data_fim`) VALUES
(1, 4, 'A - Peitoral', '2020-10-01', '2020-11-01'),
(6, 4, 'B- Posterior', '2021-02-07', '2021-03-07');

-- --------------------------------------------------------

--
-- Estrutura da tabela `treino_exercicio`
--

CREATE TABLE `treino_exercicio` (
  `id_treino` int NOT NULL,
  `id_exercicio` int NOT NULL,
  `ordem` int NOT NULL,
  `peso` int DEFAULT NULL,
  `serie` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `repeticao` int DEFAULT NULL,
  `descricao` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `treino_exercicio`
--

INSERT INTO `treino_exercicio` (`id_treino`, `id_exercicio`, `ordem`, `peso`, `serie`, `repeticao`, `descricao`) VALUES
(6, 1, 1, 50, '12', 4, 'SST'),
(6, 2, 2, 25, '12', 4, '3-7');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int NOT NULL,
  `email` varchar(50) NOT NULL,
  `senha` varchar(40) NOT NULL,
  `nivel` int NOT NULL,
  `confirmacao` varchar(40) DEFAULT NULL,
  `nome` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `endereco` varchar(80) DEFAULT NULL,
  `telefone` bigint DEFAULT NULL,
  `idade` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `email`, `senha`, `nivel`, `confirmacao`, `nome`, `endereco`, `telefone`, `idade`) VALUES
(4, 'oquesereu@outlook.com', 'dafd0d3d78ad893c92072177d4d0ee6eb716b6b9', 2, NULL, 'Altair Barbosa', 'Padre Emílio Delmi, 140', 53991455408, 23);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_usuario`);

--
-- Índices para tabela `debito`
--
ALTER TABLE `debito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pag` (`id_pag`),
  ADD KEY `id_comp` (`id_comp`);

--
-- Índices para tabela `exercicio`
--
ALTER TABLE `exercicio`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `itens`
--
ALTER TABLE `itens`
  ADD PRIMARY KEY (`id_comp`,`id_prodserv`),
  ADD KEY `id_comp` (`id_comp`),
  ADD KEY `id_prodserv` (`id_prodserv`);

--
-- Índices para tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `prodserv`
--
ALTER TABLE `prodserv`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `treino`
--
ALTER TABLE `treino`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_usuario`);

--
-- Índices para tabela `treino_exercicio`
--
ALTER TABLE `treino_exercicio`
  ADD PRIMARY KEY (`id_treino`,`id_exercicio`),
  ADD KEY `id_treino` (`id_treino`),
  ADD KEY `id_exercicio` (`id_exercicio`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `exercicio`
--
ALTER TABLE `exercicio`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `prodserv`
--
ALTER TABLE `prodserv`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `treino`
--
ALTER TABLE `treino`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Limitadores para a tabela `debito`
--
ALTER TABLE `debito`
  ADD CONSTRAINT `debito_ibfk_1` FOREIGN KEY (`id_pag`) REFERENCES `pagamento` (`id`),
  ADD CONSTRAINT `debito_ibfk_2` FOREIGN KEY (`id_comp`) REFERENCES `compra` (`id`);

--
-- Limitadores para a tabela `itens`
--
ALTER TABLE `itens`
  ADD CONSTRAINT `itens_ibfk_1` FOREIGN KEY (`id_comp`) REFERENCES `compra` (`id`),
  ADD CONSTRAINT `itens_ibfk_2` FOREIGN KEY (`id_prodserv`) REFERENCES `prodserv` (`id`);

--
-- Limitadores para a tabela `treino`
--
ALTER TABLE `treino`
  ADD CONSTRAINT `treino_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Limitadores para a tabela `treino_exercicio`
--
ALTER TABLE `treino_exercicio`
  ADD CONSTRAINT `treino_exercicio_ibfk_2` FOREIGN KEY (`id_exercicio`) REFERENCES `exercicio` (`id`),
  ADD CONSTRAINT `treino_exercicio_ibfk_3` FOREIGN KEY (`id_treino`) REFERENCES `treino` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
