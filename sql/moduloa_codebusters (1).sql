-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24-Out-2025 às 21:13
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

--
-- Extraindo dados da tabela `ativos`
--

INSERT INTO `ativos` (`ativo_id`, `nome`, `categoria`, `valor`, `data_aquisicao`, `numero_serie`, `status`, `localizacao`, `responsavel_id`, `garantia_fim`) VALUES
(5, 'Telefone do Ramon', 'Eletrônico', '5000.00', '2025-10-24', '4567', 'em uso', 'Bloco B', 2, '2025-10-31'),
(9, 'Computador', 'Eletrônico', '400.00', '2025-10-24', '1', 'em uso', 'Bloco C', 2, '2025-10-26'),
(10, 'Laptop', 'Eletrônico', '4000.00', '2025-10-24', '200', 'em uso', 'Sala 10', 4, '2025-10-31'),
(11, 'Computador', 'Eletrônico', '2000.00', '2025-10-24', '2', 'baixado', 'Sala 5', 4, '2025-10-31'),
(12, 'Iphone', 'Eletrônico', '1000.00', '2025-10-24', '90', 'em manutenção', 'Sala 15', 2, '2025-10-31'),
(13, 'Ipod', 'Eletrônico', '200.00', '2025-10-24', '9', 'disponível', 'Sala 12', 4, '2025-10-31');

--
-- Acionadores `ativos`
--
DELIMITER $$
CREATE TRIGGER `trg_ativo_adicionado` AFTER INSERT ON `ativos` FOR EACH ROW BEGIN
    INSERT INTO notificacoes (usuario_id, titulo, mensagem, tipo, lida, data_envio)
    VALUES (NEW.responsavel_id, 
            'Novo Ativo Adicionado',
            CONCAT('O ativo "', NEW.nome, '" foi adicionado ao sistema.'),
            'ativo',
            0,
            NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_novo_ativo` AFTER INSERT ON `ativos` FOR EACH ROW BEGIN
    -- Insere uma notificação para todos os usuários
    INSERT INTO notificacoes (usuario_id, titulo, mensagem, tipo, lida, data_envio)
    SELECT usuario_id,
           'Novo Ativo Adicionado' AS titulo,
           CONCAT('O ativo "', NEW.nome, '" foi adicionado ao sistema.') AS mensagem,
           'ativo' AS tipo,
           0 AS lida,
           NOW() AS data_envio
    FROM usuarios;
END
$$
DELIMITER ;

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

--
-- Extraindo dados da tabela `manutencoes`
--

INSERT INTO `manutencoes` (`manutencao_id`, `ativo_id`, `tipo`, `data`, `responsavel_tecnico`, `custo`, `descricao`, `criado_por`, `data_registro`) VALUES
(1, 9, 'corretiva', '2025-10-24', 'Kaíque', '6.50', '2', 1, '2025-10-24 11:32:49'),
(2, 5, 'corretiva', '2025-10-24', 'Kaíque', '6.50', 'oi', 1, '2025-10-24 11:33:09'),
(3, 5, 'preventiva', '2025-10-23', 'Jonas', '400.00', 'Manutenção de sensores', 1, '2025-10-24 11:39:09'),
(4, 11, 'preventiva', '2025-10-24', 'Kaíque', '300.00', 'Ajeitando sensores', 1, '2025-10-24 12:27:17');

--
-- Acionadores `manutencoes`
--
DELIMITER $$
CREATE TRIGGER `trg_manutencao_adicionada` AFTER INSERT ON `manutencoes` FOR EACH ROW BEGIN
    INSERT INTO notificacoes (usuario_id, titulo, mensagem, tipo, lida, data_envio)
    VALUES (NEW.criado_por,
            'Nova Manutenção Registrada',
            CONCAT('Manutenção do ativo "', (SELECT nome FROM ativos WHERE ativo_id = NEW.ativo_id), '" registrada.'),
            'manutencao',
            0,
            NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_nova_manutencao` AFTER INSERT ON `manutencoes` FOR EACH ROW BEGIN
    -- Insere uma notificação para todos os usuários
    INSERT INTO notificacoes (usuario_id, titulo, mensagem, tipo, lida, data_envio)
    SELECT usuario_id,
           'Nova Manutenção' AS titulo,
           CONCAT('Uma manutenção do tipo "', NEW.tipo, '" foi registrada para o ativo "', (SELECT nome FROM ativos WHERE ativo_id = NEW.ativo_id), '".') AS mensagem,
           'manutencao' AS tipo,
           0 AS lida,
           NOW() AS data_envio
    FROM usuarios;
END
$$
DELIMITER ;

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

--
-- Extraindo dados da tabela `notificacoes`
--

INSERT INTO `notificacoes` (`notificacao_id`, `usuario_id`, `titulo`, `mensagem`, `tipo`, `lida`, `data_envio`) VALUES
(1, 4, 'Novo Ativo Adicionado', 'O ativo \"Computador\" foi adicionado ao sistema.', '', 0, '2025-10-24 12:25:31'),
(2, 1, 'Nova Manutenção Registrada', 'Manutenção do ativo \"Computador\" registrada.', 'manutencao', 0, '2025-10-24 12:27:17'),
(3, 2, 'Novo Ativo Adicionado', 'O ativo \"Iphone\" foi adicionado ao sistema.', '', 0, '2025-10-24 12:28:05'),
(4, 4, 'Novo Ativo Adicionado', 'O ativo \"Ipod\" foi adicionado ao sistema.', '', 0, '2025-10-24 12:32:56'),
(5, 4, 'Novo Ativo Adicionado', 'O ativo \"Ipod\" foi adicionado ao sistema.', '', 0, '2025-10-24 12:32:56'),
(6, 2, 'Novo Ativo Adicionado', 'O ativo \"Ipod\" foi adicionado ao sistema.', '', 0, '2025-10-24 12:32:56'),
(7, 1, 'Novo Ativo Adicionado', 'O ativo \"Ipod\" foi adicionado ao sistema.', '', 0, '2025-10-24 12:32:56');

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
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `nome`, `email`, `senha_hash`, `nivel_permissao`, `setor`) VALUES
(1, 'Zubeldia', 'zuzu@gmail.com', '$2y$10$vv2WLtXiPxqoF0C.kv1wV.JNI94PvX.phjfYMv6qTVqFTBwxoatYG', 'admin', 'TI'),
(2, 'Ramon', 'ramon@gmail.com', '$2y$10$tOF.E..Y43wlQ7xBdA3e8ummDIgxm8VLEmNeENyHImKfmk5bmlQkS', 'colaborador', 'Máquinário'),
(4, 'Joao Pedro', 'joao@gmail.com', '$2y$10$rPIij5FwkzXUD1sfvJlbiezyTBXiD/euvCFroMpSIvZyzvcPO9sYK', 'colaborador', 'Comercial'),
(5, 'João Gustavo Mota Ramos', 'joaogustavo2202@gmail.com', '$2y$10$iYvZ36orXS/Gh9cOcGbxLe6vk1J3RFBFpWlr1eNKqDxj8C2ap.eGO', 'colaborador', 'Ti'),
(6, 'Gabriel Moreira', 'gabriel@gmail.com', '$2y$10$0BFraHw5HPoyFtu/A8gXouqx.gISMSFFJfUXXDUdxWmoFaQd.ZHTS', 'admin', 'TI');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `ativos`
--
ALTER TABLE `ativos`
  ADD PRIMARY KEY (`ativo_id`),
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
  MODIFY `ativo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
  MODIFY `manutencao_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `notificacao_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
