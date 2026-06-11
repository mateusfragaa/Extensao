-- Copiando estrutura do banco de dados para extensao
CREATE DATABASE IF NOT EXISTS `extensao` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `extensao`;

-- Copiando estrutura para tabela extensao.tb_pagamento
CREATE TABLE IF NOT EXISTS `tb_pagamento` (
  `PAG_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PAG_VALOR` decimal(10,2) unsigned NOT NULL,
  `PAG_CREATED_AT` datetime NOT NULL DEFAULT current_timestamp(),
  `PAG_STATUS` char(1) NOT NULL DEFAULT 'A' COMMENT 'A= ABERTO; P =PAGO; C = CANCELADO',
  `PAG_VALOR_PAGO` decimal(10,2) DEFAULT 0.00,
  `PAG_VALOR_ABERTO` decimal(10,2) GENERATED ALWAYS AS (`PAG_VALOR`) VIRTUAL,
  `PAG_FAVORECIDO_ID` int(10) unsigned NOT NULL,
  `PAG_OBSERVACAO` text DEFAULT NULL,
  `PAG_DATA_BAIXA` datetime DEFAULT NULL,
  PRIMARY KEY (`PAG_ID`),
  UNIQUE KEY `REC_ID_UNIQUE` (`PAG_ID`),
  KEY `PAG_FAVORECIDO_idx` (`PAG_FAVORECIDO_ID`),
  CONSTRAINT `PAG_FAVORECIDO` FOREIGN KEY (`PAG_FAVORECIDO_ID`) REFERENCES `tb_pessoa` (`PES_ID`) ON UPDATE CASCADE
);

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_pagamento_item
CREATE TABLE IF NOT EXISTS `tb_pagamento_item` (
  `PAGI_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PAGI_PAG_ID` int(10) unsigned NOT NULL,
  `PAGI_VALOR` decimal(10,2) unsigned NOT NULL,
  `PAGI_CREATED_AT` datetime NOT NULL DEFAULT current_timestamp(),
  `PAGI_TIPO_DOCUMENTO` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PAGI_ID`),
  UNIQUE KEY `RECI_ID_UNIQUE` (`PAGI_ID`),
  KEY `PAGI_TIPO_DOCUMENTO_idx` (`PAGI_TIPO_DOCUMENTO`),
  KEY `PAGI_PAG_ID_idx` (`PAGI_PAG_ID`),
  CONSTRAINT `PAGI_PAG_ID` FOREIGN KEY (`PAGI_PAG_ID`) REFERENCES `tb_pagamento` (`PAG_ID`) ON UPDATE CASCADE,
  CONSTRAINT `PAGI_TIPO_DOCUMENTO` FOREIGN KEY (`PAGI_TIPO_DOCUMENTO`) REFERENCES `tb_tipo_documento` (`TDC_ID`)
);  

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_pedido_venda
CREATE TABLE IF NOT EXISTS tb_pedido_venda (
  PEV_ID INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  PEV_DATA_VENDA DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PEV_TOTAL DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  PEV_STATUS CHAR(1) NOT NULL DEFAULT 'A' COMMENT 'A = ABERTO; F = FATURADA; C = CANCELADA',
  PEV_ACRESCIMO DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
  PEV_DESCONTO DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
  PEV_CLIENTE_ID INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (PEV_ID),
  CONSTRAINT PEV_CLIENTE_ID FOREIGN KEY (PEV_CLIENTE_ID) REFERENCES tb_pessoa (PES_ID) ON UPDATE CASCADE
);

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_pedido_venda_item
CREATE TABLE IF NOT EXISTS `tb_pedido_venda_item` (
  `PEVI_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PEVI_VENDA_ID` int(10) unsigned NOT NULL,
  `PEVI_PRD_ID` int(10) unsigned NOT NULL,
  `PEVI_QUANTIDADE` decimal(10,2) unsigned NOT NULL,
  `PEVI_PRECO_UNITARIO` decimal(10,2) unsigned NOT NULL,
  `PEVI_SUBTOTAL` decimal(10,2) GENERATED ALWAYS AS (`PEVI_QUANTIDADE` * `PEVI_PRECO_UNITARIO`) VIRTUAL,
  PRIMARY KEY (`PEVI_ID`),
  UNIQUE KEY `PEVI_ID_UNIQUE` (`PEVI_ID`),
  KEY `PEVI_VENDA_ID_idx` (`PEVI_VENDA_ID`),
  KEY `PEVI_PRD_ID_idx` (`PEVI_PRD_ID`),
  CONSTRAINT `PEVI_PRD_ID` FOREIGN KEY (`PEVI_PRD_ID`) REFERENCES `tb_produto` (`PRD_ID`) ON UPDATE CASCADE,
  CONSTRAINT `PEVI_VENDA_ID` FOREIGN KEY (`PEVI_VENDA_ID`) REFERENCES `tb_pedido_venda` (`PEV_ID`) ON DELETE CASCADE
);

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_pessoa
CREATE TABLE IF NOT EXISTS `tb_pessoa` (
  `PES_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PES_NOME` varchar(45) NOT NULL,
  `CPF_CNPJ` varchar(14) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `TELEFONE` varchar(9) DEFAULT NULL,
  `TIPO_PESSOA` char(1) DEFAULT 'F' COMMENT 'F=Fisica;J=Juridica',
  `CEP` varchar(8) DEFAULT NULL,
  `ENDERECO` varchar(50) DEFAULT NULL,
  `NUMERO` varchar(6) DEFAULT NULL,
  `CIDADE` varchar(40) DEFAULT NULL,
  `BAIRRO` varchar(40) DEFAULT NULL,
  `UF` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`PES_ID`),
  UNIQUE KEY `CLI_ID_UNIQUE` (`PES_ID`),
  UNIQUE KEY `CPF_CNPJ` (`CPF_CNPJ`)
);

//Mudança feita para permitir auto incremento na tabela tb_pessoa, pois o mesmo estava desabilitado.
ALTER TABLE tb_pessoa MODIFY PES_ID int(10) unsigned NOT NULL AUTO_INCREMENT;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_produto
CREATE TABLE IF NOT EXISTS `tb_produto` (
  `PRD_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PRD_DESCRICAO` varchar(45) NOT NULL,
  `PRD_ESTOQUE` decimal(10,3) unsigned DEFAULT 0.000,
  `PRD_STATUS` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = ATIVO; 0 = INATIVO',
  `PRD_PRECO_VENDA` decimal(10,2) unsigned DEFAULT 0.00,
  `PRD_PRECO_CUSTO` decimal(10,2) unsigned DEFAULT 0.00,
  `PRD_UNIDADE` varchar(10) DEFAULT 'UN',
  `PRD_CREATED_AT` datetime DEFAULT current_timestamp(),
  `PRD_CATEGORIA` varchar(45) DEFAULT NULL,
  `PRD_OBSERVACAO` text DEFAULT NULL,
  `PRD_ESTOQUE_MIN` decimal(10,3) unsigned DEFAULT NULL,
  PRIMARY KEY (`PRD_ID`),
  UNIQUE KEY `PRD_DESCRICAO_UNIQUE` (`PRD_DESCRICAO`),
  UNIQUE KEY `PRD_ID_UNIQUE` (`PRD_ID`)
);

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_recebimento
CREATE TABLE IF NOT EXISTS `tb_recebimento` (
  `REC_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `REC_VENDA_ID` int(10) unsigned NOT NULL,
  `REC_VALOR` decimal(10,2) unsigned NOT NULL,
  `REC_CREATED_AT` datetime NOT NULL DEFAULT current_timestamp(),
  `REC_STATUS` char(1) NOT NULL DEFAULT 'A' COMMENT 'A= ABERTO; B = BAIXADO; C = CANCELADO',
  `REC_VALOR_PAGO` decimal(10,2) DEFAULT 0.00,
  `REC_VALOR_ABERTO` decimal(10,2) GENERATED ALWAYS AS (`REC_VALOR`) VIRTUAL,
  `REC_OBSERVACAO` text DEFAULT NULL,
  `REC_DEVEDOR_ID` int(10) unsigned NOT NULL,
  `REC_DATA_BAIXA` datetime DEFAULT NULL,
  PRIMARY KEY (`REC_ID`),
  UNIQUE KEY `REC_ID_UNIQUE` (`REC_ID`),
  KEY `REC_VENDA_ID_idx` (`REC_VENDA_ID`),
  KEY `REC_DEVEDOR_ID_idx` (`REC_DEVEDOR_ID`),
  CONSTRAINT `REC_DEVEDOR_ID` FOREIGN KEY (`REC_DEVEDOR_ID`) REFERENCES `tb_pessoa` (`PES_ID`) ON UPDATE CASCADE,
  CONSTRAINT `REC_VENDA_ID` FOREIGN KEY (`REC_VENDA_ID`) REFERENCES `tb_pedido_venda` (`PEV_ID`)
);

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_recebimento_item
CREATE TABLE IF NOT EXISTS `tb_recebimento_item` (
  `RECI_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RECI_REC_ID` int(10) unsigned NOT NULL,
  `RECI_VALOR` decimal(10,2) unsigned NOT NULL,
  `RECI_CREATED_AT` datetime DEFAULT current_timestamp(),
  `RECI_TIPO_DOCUMENTO` int(10) unsigned NOT NULL,
  PRIMARY KEY (`RECI_ID`),
  UNIQUE KEY `RECI_ID_UNIQUE` (`RECI_ID`),
  KEY `RECI_REC_ID_idx` (`RECI_REC_ID`),
  KEY `RECI_TIPO_DOCUMENTO_idx` (`RECI_TIPO_DOCUMENTO`),
  CONSTRAINT `RECI_REC_ID` FOREIGN KEY (`RECI_REC_ID`) REFERENCES `tb_recebimento` (`REC_ID`) ON UPDATE CASCADE,
  CONSTRAINT `RECI_TIPO_DOCUMENTO` FOREIGN KEY (`RECI_TIPO_DOCUMENTO`) REFERENCES `tb_tipo_documento` (`TDC_ID`)
);

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_tipo_documento
CREATE TABLE IF NOT EXISTS `tb_tipo_documento` (
  `TDC_ID` int(10) unsigned NOT NULL,
  `TDC_DESCRICAO` varchar(45) NOT NULL,
  `TDC_STATUS` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '1 = ATIVO; 0 = INATIVO',
  PRIMARY KEY (`TDC_ID`),
  UNIQUE KEY `TDC_ID_UNIQUE` (`TDC_ID`),
  UNIQUE KEY `TDC_DESCRICAO_UNIQUE` (`TDC_DESCRICAO`)
);

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_usuario
CREATE TABLE IF NOT EXISTS `tb_usuario` (
  `USU_ID` int(10) unsigned NOT NULL,
  `USU_NOME` varchar(45) NOT NULL,
  `USU_SENHA` varchar(45) NOT NULL,
  PRIMARY KEY (`USU_ID`),
  UNIQUE KEY `USU_ID_UNIQUE` (`USU_ID`),
  UNIQUE KEY `USU_NOME_UNIQUE` (`USU_NOME`),
  UNIQUE KEY `USU_SENHA_UNIQUE` (`USU_SENHA`)
);
