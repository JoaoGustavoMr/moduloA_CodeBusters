-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24-Out-2025 às 13:46
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `moduloa_codebusters`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `ativos`
--

CREATE TABLE `ativos` (
  `ativo_id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `valor` decimal(12,2) DEFAULT NULL,
  `data_aquisicao` date DEFAULT NULL,
  `numero_serie` varchar(100) DEFAULT NULL,
  `status` enum('em uso','em manutenção','disponível','baixado') DEFAULT 'disponível',
  `localizacao` varchar(150) DEFAULT NULL,
  `responsavel_id` int(11) DEFAULT NULL,
  `garantia_fim` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `chat_assistente`
--

CREATE TABLE `chat_assistente` (
  `chat_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `pergunta` text NOT NULL,
  `resposta` text DEFAULT NULL,
  `data_interacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_localizacao`
--

CREATE TABLE `historico_localizacao` (
  `historico_id` int(11) NOT NULL,
  `ativo_id` int(11) NOT NULL,
  `responsavel_anterior_id` int(11) DEFAULT NULL,
  `responsavel_atual_id` int(11) DEFAULT NULL,
  `local_anterior` varchar(150) DEFAULT NULL,
  `local_atual` varchar(150) DEFAULT NULL,
  `data_movimentacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `insights_ia`
--

CREATE TABLE `insights_ia` (
  `insight_id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('custo_alto','ativo_critico','otimizacao') NOT NULL,
  `data_geracao` datetime DEFAULT current_timestamp(),
  `ativo_relacionado_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `manutencoes`
--

CREATE TABLE `manutencoes` (
  `manutencao_id` int(11) NOT NULL,
  `ativo_id` int(11) NOT NULL,
  `tipo` enum('corretiva','preventiva') NOT NULL,
  `data` date NOT NULL,
  `responsavel_tecnico` varchar(150) DEFAULT NULL,
  `custo` decimal(10,2) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `criado_por` int(11) DEFAULT NULL,
  `data_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `notificacao_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensagem` text NOT NULL,
  `tipo` enum('manutencao','garantia','licenca','devolucao','alerta_ia') NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_envio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `previsoes_ia`
--

CREATE TABLE `previsoes_ia` (
  `previsao_id` int(11) NOT NULL,
  `ativo_id` int(11) NOT NULL,
  `probabilidade_falha` decimal(5,2) DEFAULT NULL CHECK (`probabilidade_falha` >= 0 and `probabilidade_falha` <= 100),
  `data_previsao` datetime DEFAULT current_timestamp(),
  `status` enum('pendente','em risco','estável') DEFAULT 'pendente',
  `recomendacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `relatorios`
--

CREATE TABLE `relatorios` (
  `relatorio_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `filtros_aplicados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`filtros_aplicados`)),
  `data_geracao` datetime DEFAULT current_timestamp(),
  `arquivo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `nivel_permissao` enum('admin','colaborador') NOT NULL,
  `setor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `ativos`
--
ALTER TABLE `ativos`
  ADD PRIMARY KEY (`ativo_id`),
  ADD UNIQUE KEY `numero_serie` (`numero_serie`),
  ADD KEY `responsavel_id` (`responsavel_id`);

--
-- Índices para tabela `chat_assistente`
--
ALTER TABLE `chat_assistente`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `historico_localizacao`
--
ALTER TABLE `historico_localizacao`
  ADD PRIMARY KEY (`historico_id`),
  ADD KEY `ativo_id` (`ativo_id`),
  ADD KEY `responsavel_anterior_id` (`responsavel_anterior_id`),
  ADD KEY `responsavel_atual_id` (`responsavel_atual_id`);

--
-- Índices para tabela `insights_ia`
--
ALTER TABLE `insights_ia`
  ADD PRIMARY KEY (`insight_id`),
  ADD KEY `ativo_relacionado_id` (`ativo_relacionado_id`);

--
-- Índices para tabela `manutencoes`
--
ALTER TABLE `manutencoes`
  ADD PRIMARY KEY (`manutencao_id`),
  ADD KEY `ativo_id` (`ativo_id`),
  ADD KEY `criado_por` (`criado_por`);

--
-- Índices para tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`notificacao_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `previsoes_ia`
--
ALTER TABLE `previsoes_ia`
  ADD PRIMARY KEY (`previsao_id`),
  ADD KEY `ativo_id` (`ativo_id`);

--
-- Índices para tabela `relatorios`
--
ALTER TABLE `relatorios`
  ADD PRIMARY KEY (`relatorio_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `ativos`
--
ALTER TABLE `ativos`
  MODIFY `ativo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `chat_assistente`
--
ALTER TABLE `chat_assistente`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_localizacao`
--
ALTER TABLE `historico_localizacao`
  MODIFY `historico_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `insights_ia`
--
ALTER TABLE `insights_ia`
  MODIFY `insight_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `manutencoes`
--
ALTER TABLE `manutencoes`
  MODIFY `manutencao_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `notificacao_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `previsoes_ia`
--
ALTER TABLE `previsoes_ia`
  MODIFY `previsao_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `relatorios`
--
ALTER TABLE `relatorios`
  MODIFY `relatorio_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `ativos`
--
ALTER TABLE `ativos`
  ADD CONSTRAINT `ativos_ibfk_1` FOREIGN KEY (`responsavel_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `chat_assistente`
--
ALTER TABLE `chat_assistente`
  ADD CONSTRAINT `chat_assistente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `historico_localizacao`
--
ALTER TABLE `historico_localizacao`
  ADD CONSTRAINT `historico_localizacao_ibfk_1` FOREIGN KEY (`ativo_id`) REFERENCES `ativos` (`ativo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historico_localizacao_ibfk_2` FOREIGN KEY (`responsavel_anterior_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `historico_localizacao_ibfk_3` FOREIGN KEY (`responsavel_atual_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `insights_ia`
--
ALTER TABLE `insights_ia`
  ADD CONSTRAINT `insights_ia_ibfk_1` FOREIGN KEY (`ativo_relacionado_id`) REFERENCES `ativos` (`ativo_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `manutencoes`
--
ALTER TABLE `manutencoes`
  ADD CONSTRAINT `manutencoes_ibfk_1` FOREIGN KEY (`ativo_id`) REFERENCES `ativos` (`ativo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `manutencoes_ibfk_2` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `previsoes_ia`
--
ALTER TABLE `previsoes_ia`
  ADD CONSTRAINT `previsoes_ia_ibfk_1` FOREIGN KEY (`ativo_id`) REFERENCES `ativos` (`ativo_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `relatorios`
--
ALTER TABLE `relatorios`
  ADD CONSTRAINT `relatorios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
