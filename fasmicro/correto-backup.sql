-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           11.5.2-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.13.0.7147
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para extensao
CREATE DATABASE IF NOT EXISTS `extensao` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `extensao`;

-- Copiando estrutura para procedure extensao.ATUALIZAR_SUB_TOTAL_PEDIDO_VENDA
DELIMITER //
CREATE PROCEDURE `ATUALIZAR_SUB_TOTAL_PEDIDO_VENDA`(IN VENDA INT)
BEGIN
    DECLARE V_NOVO_SUBTOTAL DECIMAL(10,2);
    SELECT 
        COALESCE(SUM(PEVI_SUBTOTAL),0) 
        INTO V_NOVO_SUBTOTAL 
    FROM TB_PEDIDO_VENDA_ITEM 
    WHERE PEVI_VENDA_ID = VENDA;  
    
    UPDATE 
        TB_PEDIDO_VENDA 
        SET PEV_SUB_TOTAL = V_NOVO_SUBTOTAL WHERE PEV_ID = VENDA;
END//
DELIMITER ;

-- Copiando estrutura para procedure extensao.pr_recalcular_pagamento
DELIMITER //
CREATE PROCEDURE `pr_recalcular_pagamento`(
    IN p_pag_id INT UNSIGNED
)
BEGIN
    DECLARE v_valor_pago DECIMAL(10,2);


    -- Soma somente os itens pagos
    SELECT COALESCE(SUM(PAGI_VALOR), 0)
    INTO v_valor_pago
    FROM tb_pagamento_item
    WHERE PAGI_PAG_ID = p_pag_id
      AND PAGI_STATUS = 'P';


    -- Atualiza o pagamento pai
    UPDATE tb_pagamento
    SET
        PAG_VALOR_PAGO = v_valor_pago,
        PAG_VALOR_ABERTO = PAG_VALOR - v_valor_pago,
        PAG_STATUS = CASE
                        WHEN PAG_STATUS = 'C' THEN 'C'
                        WHEN (PAG_VALOR - v_valor_pago) <= 0 THEN 'P'
                        ELSE 'A'
                     END
    WHERE PAG_ID = p_pag_id;


END//
DELIMITER ;

-- Copiando estrutura para procedure extensao.sp_add_produto_pedido
DELIMITER //
CREATE PROCEDURE `sp_add_produto_pedido`(
    IN p_venda_id INT,
    IN p_produto_id INT,
    IN p_quantidade DECIMAL(10,2)
)
proc:BEGIN

    DECLARE v_estoque DECIMAL(10,3);
    DECLARE v_preco DECIMAL(10,2);
    DECLARE v_reservado DECIMAL(10,2);
    DECLARE v_existente DECIMAL(10,2);
    DECLARE v_disponivel DECIMAL(10,2);

    /* Produto */
    SELECT
        PRD_ESTOQUE,
        PRD_PRECO_VENDA
    INTO
        v_estoque,
        v_preco
    FROM tb_produto
    WHERE PRD_ID = p_produto_id
      AND PRD_STATUS = 1;

    IF v_estoque IS NULL THEN

        SELECT
            0 AS sucesso,
            'Produto não encontrado.' AS mensagem;

        LEAVE proc;

    END IF;

    /* Reserva em outros pedidos */
    SELECT
        COALESCE(SUM(i.PEVI_QUANTIDADE),0)
    INTO v_reservado
    FROM tb_pedido_venda_item i
        INNER JOIN tb_pedido_venda v
            ON v.PEV_ID = i.PEVI_VENDA_ID
    WHERE i.PEVI_PRD_ID = p_produto_id
      AND v.PEV_STATUS IN ('A','O')
      AND v.PEV_ID <> p_venda_id;

    /* Quantidade já existente neste pedido */
    SELECT
        COALESCE(PEVI_QUANTIDADE,0)
    INTO v_existente
    FROM tb_pedido_venda_item
    WHERE PEVI_VENDA_ID = p_venda_id
      AND PEVI_PRD_ID = p_produto_id
    LIMIT 1;

    SET v_disponivel = v_estoque - v_reservado;

    IF (v_existente + p_quantidade) > v_disponivel THEN

        SELECT
            0 AS sucesso,
            CONCAT(
                'Estoque insuficiente. Disponível: ',
                FORMAT(v_disponivel - v_existente,2),
                ' unidade(s).'
            ) AS mensagem;

        LEAVE proc;

    END IF;

    /* Atualiza caso o produto já exista */
    IF EXISTS(
        SELECT 1
        FROM tb_pedido_venda_item
        WHERE PEVI_VENDA_ID = p_venda_id
          AND PEVI_PRD_ID = p_produto_id
    ) THEN

        UPDATE tb_pedido_venda_item
        SET PEVI_QUANTIDADE = PEVI_QUANTIDADE + p_quantidade
        WHERE PEVI_VENDA_ID = p_venda_id
          AND PEVI_PRD_ID = p_produto_id;

    ELSE

        INSERT INTO tb_pedido_venda_item(
            PEVI_VENDA_ID,
            PEVI_PRD_ID,
            PEVI_QUANTIDADE,
            PEVI_PRECO_UNITARIO
        )
        VALUES(
            p_venda_id,
            p_produto_id,
            p_quantidade,
            v_preco
        );

    END IF;

    /* Atualiza o subtotal da venda */
    CALL ATUALIZAR_SUB_TOTAL_PEDIDO_VENDA(p_venda_id);

    SELECT
        1 AS sucesso,
        'Produto adicionado com sucesso.' AS mensagem;

END//
DELIMITER ;

-- Copiando estrutura para procedure extensao.sp_baixar_recebimento
DELIMITER //
CREATE PROCEDURE `sp_baixar_recebimento`(
    IN p_json_ids JSON,
    IN p_valor_total DECIMAL(10,2),
    IN p_forma_pagamento INT
)
proc:BEGIN
    DECLARE v_saldo DECIMAL(10,2);
    DECLARE v_id INT;
    DECLARE v_valor_total_rec DECIMAL(10,2);
    DECLARE v_valor_pago_rec DECIMAL(10,2);
    DECLARE v_pendente DECIMAL(10,2);
    DECLARE v_pagar_agora DECIMAL(10,2);
    
    -- Variáveis de controle de status e data
    DECLARE v_novo_status CHAR(1);
    DECLARE v_nova_data_baixa DATETIME;
    
    DECLARE v_done INT DEFAULT FALSE;
    
    DECLARE cur_ids CURSOR FOR 
        SELECT id FROM JSON_TABLE(p_json_ids, '$[*]' COLUMNS(id INT PATH '$')) AS jt;
        
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 AS sucesso, 'Erro ao processar baixa de recebimentos.' AS mensagem;
    END;

    SET v_saldo = p_valor_total;

    START TRANSACTION;

    OPEN cur_ids;

    read_loop: LOOP
        FETCH cur_ids INTO v_id;
        
        IF v_done OR v_saldo <= 0 THEN
            LEAVE read_loop;
        END IF;

        -- Busca valores atuais, incluindo a data de baixa atual para não sobrescrever com null se já existir
        SELECT REC_VALOR, COALESCE(REC_VALOR_PAGO, 0), REC_DATA_BAIXA
        INTO v_valor_total_rec, v_valor_pago_rec, v_nova_data_baixa
        FROM tb_recebimento 
        WHERE REC_ID = v_id FOR UPDATE;

        IF v_valor_total_rec IS NOT NULL THEN
            
            SET v_pendente = v_valor_total_rec - v_valor_pago_rec;

            IF v_pendente > 0 THEN
                
                -- Define os novos valores baseado se o saldo cobre a pendência
                IF v_saldo >= v_pendente THEN
                    SET v_pagar_agora = v_pendente;
                    SET v_novo_status = 'B';
                    SET v_nova_data_baixa = NOW(); -- Preenche a data porque baixou total
                ELSE
                    SET v_pagar_agora = v_saldo;
                    SET v_novo_status = 'P';
                    -- Mantém v_nova_data_baixa inalterada caso já houvesse data ou nula se não houvesse
                END IF;

                -- O Update agora fica simples, direto e sem dupla checagem matemática
                UPDATE tb_recebimento
                SET 
                    REC_VALOR_PAGO = REC_VALOR_PAGO + v_pagar_agora,
                    REC_STATUS = v_novo_status,
                    REC_DATA_BAIXA = v_nova_data_baixa
                WHERE REC_ID = v_id;

                INSERT INTO tb_recebimento_item (
                    RECI_REC_ID, 
                    RECI_VALOR, 
                    RECI_TIPO_DOCUMENTO
                ) VALUES (
                    v_id, 
                    v_pagar_agora, 
                    p_forma_pagamento
                );

                SET v_saldo = v_saldo - v_pagar_agora;
                
            END IF;
        END IF;
    END LOOP;

    CLOSE cur_ids;

    COMMIT;

    SELECT 1 AS sucesso, 'Baixa processada com sucesso.' AS mensagem;

END//
DELIMITER ;

-- Copiando estrutura para procedure extensao.sp_cancelar_venda
DELIMITER //
CREATE PROCEDURE `sp_cancelar_venda`(
    IN p_id_venda INT
)
proc:BEGIN

    DECLARE v_status CHAR(1);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Erro ao cancelar venda.' AS mensagem;
    END;

    START TRANSACTION;

    SELECT PEV_STATUS
    INTO v_status
    FROM tb_pedido_venda
    WHERE PEV_ID = p_id_venda;

    IF v_status IS NULL THEN

        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Venda não encontrada.' AS mensagem;

        LEAVE proc;

    END IF;

    IF v_status = 'C' THEN

        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Venda já está cancelada.' AS mensagem;

        LEAVE proc;

    END IF;

    /*
        Devolve produtos ao estoque
    */
    UPDATE tb_produto p
    INNER JOIN tb_pedido_venda_item i
        ON i.PEVI_PRD_ID = p.PRD_ID
    SET
        p.PRD_ESTOQUE = p.PRD_ESTOQUE + i.PEVI_QUANTIDADE
    WHERE i.PEVI_VENDA_ID = p_id_venda;

    /*
        Cancela todos os recebimentos
    */
    UPDATE tb_recebimento
    SET
        REC_STATUS = 'C'
    WHERE REC_VENDA_ID = p_id_venda;

    /*
        Cancela a venda
    */
    UPDATE tb_pedido_venda
    SET
        PEV_STATUS = 'C'
    WHERE PEV_ID = p_id_venda;

    COMMIT;

    SELECT
        1 AS sucesso,
        'Venda cancelada com sucesso.' AS mensagem;

END proc//
DELIMITER ;

-- Copiando estrutura para procedure extensao.sp_descontar_estoque
DELIMITER //
CREATE PROCEDURE `sp_descontar_estoque`(
    IN p_id_venda INT
)
proc:BEGIN

    DECLARE v_qtd_itens INT;
    DECLARE v_sem_estoque INT;

    SELECT COUNT(*)
    INTO v_qtd_itens
    FROM tb_pedido_venda_item
    WHERE PEVI_VENDA_ID = p_id_venda;

    IF v_qtd_itens = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'A venda não possui itens.';
    END IF;

    SELECT COUNT(*)
    INTO v_sem_estoque
    FROM tb_pedido_venda_item i
    INNER JOIN tb_produto p
        ON p.PRD_ID = i.PEVI_PRD_ID
    WHERE i.PEVI_VENDA_ID = p_id_venda
      AND p.PRD_ESTOQUE < i.PEVI_QUANTIDADE;

    IF v_sem_estoque > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Estoque insuficiente para faturar a venda.';
    END IF;

    UPDATE tb_produto p
    INNER JOIN tb_pedido_venda_item i
        ON i.PEVI_PRD_ID = p.PRD_ID
    SET
        p.PRD_ESTOQUE = p.PRD_ESTOQUE - i.PEVI_QUANTIDADE
    WHERE i.PEVI_VENDA_ID = p_id_venda;

END proc//
DELIMITER ;

-- Copiando estrutura para procedure extensao.sp_estornar_estoque
DELIMITER //
CREATE PROCEDURE `sp_estornar_estoque`(
    IN p_id_venda INT
)
proc:BEGIN

    DECLARE v_existe INT;

    SELECT COUNT(*)
    INTO v_existe
    FROM tb_pedido_venda
    WHERE PEV_ID = p_id_venda;

    IF v_existe = 0 THEN

        SELECT
            0 AS sucesso,
            'Venda não encontrada.' AS mensagem;

        LEAVE proc;

    END IF;

    UPDATE tb_produto p
    INNER JOIN tb_pedido_venda_item i
        ON i.PEVI_PRD_ID = p.PRD_ID
    SET
        p.PRD_ESTOQUE = p.PRD_ESTOQUE + i.PEVI_QUANTIDADE
    WHERE i.PEVI_VENDA_ID = p_id_venda;

    SELECT
        1 AS sucesso,
        'Estoque estornado com sucesso.' AS mensagem;

END proc//
DELIMITER ;

-- Copiando estrutura para procedure extensao.sp_estornar_recebimento_item
DELIMITER //
CREATE PROCEDURE `sp_estornar_recebimento_item`(
    IN p_json_ids JSON
)
proc:BEGIN
    DECLARE v_id INT;
    DECLARE v_rec_id INT;
    DECLARE v_item_valor DECIMAL(10,2);
    
    DECLARE v_rec_valor_total DECIMAL(10,2);
    DECLARE v_rec_valor_pago DECIMAL(10,2);
    DECLARE v_novo_valor_pago DECIMAL(10,2);
    
    DECLARE v_novo_status CHAR(1);
    DECLARE v_nova_data_baixa DATETIME;
    
    DECLARE v_done INT DEFAULT FALSE;
    
    -- Cursor para varrer os IDs dos ITENS que serão apagados
    DECLARE cur_ids CURSOR FOR 
        SELECT id FROM JSON_TABLE(p_json_ids, '$[*]' COLUMNS(id INT PATH '$')) AS jt;
        
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;

    -- Proteção com Rollback em caso de erro
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 AS sucesso, 'Erro ao processar o estorno dos recebimentos.' AS mensagem;
    END;

    START TRANSACTION;

    OPEN cur_ids;

    read_loop: LOOP
        FETCH cur_ids INTO v_id;
        
        IF v_done THEN
            LEAVE read_loop;
        END IF;

        -- 1. Reseta as variáveis a cada loop por segurança e busca os dados do Item
        SET v_rec_id = NULL;
        SET v_item_valor = 0;
        
        SELECT RECI_REC_ID, RECI_VALOR
        INTO v_rec_id, v_item_valor
        FROM tb_recebimento_item 
        WHERE RECI_ID = v_id;

        -- 2. Se encontrou o item, prossegue com o estorno
        IF v_rec_id IS NOT NULL THEN
            
            -- Busca os dados do pai e trava a linha (FOR UPDATE)
            SELECT REC_VALOR, COALESCE(REC_VALOR_PAGO, 0), REC_DATA_BAIXA
            INTO v_rec_valor_total, v_rec_valor_pago, v_nova_data_baixa
            FROM tb_recebimento
            WHERE REC_ID = v_rec_id FOR UPDATE;
            
            -- Calcula o novo valor pago (protegendo para não ficar menor que zero)
            SET v_novo_valor_pago = v_rec_valor_pago - v_item_valor;
            IF v_novo_valor_pago < 0 THEN
                SET v_novo_valor_pago = 0;
            END IF;

            -- 3. Define o status e a data baseado no que restou
            IF v_novo_valor_pago <= 0 THEN
                SET v_novo_status = 'A'; -- Zerou tudo, volta para Aberto
                SET v_nova_data_baixa = NULL; -- Remove a data de baixa
            ELSEIF v_novo_valor_pago < v_rec_valor_total THEN
                SET v_novo_status = 'P'; -- Ainda tem algo pago, fica Parcial
            ELSE
                SET v_novo_status = 'B'; -- Por segurança, se der match no valor total, mantém Baixado
            END IF;

            -- 4. Atualiza o recebimento pai
            UPDATE tb_recebimento
            SET 
                REC_VALOR_PAGO = v_novo_valor_pago,
                REC_STATUS = v_novo_status,
                REC_DATA_BAIXA = v_nova_data_baixa
            WHERE REC_ID = v_rec_id;

            -- 5. Apaga fisicamente o registro do item estornado
            DELETE FROM tb_recebimento_item WHERE RECI_ID = v_id;
            
        END IF;
    END LOOP;

    CLOSE cur_ids;

    COMMIT;

    SELECT 1 AS sucesso, 'Estorno processado com sucesso.' AS mensagem;

END//
DELIMITER ;

-- Copiando estrutura para procedure extensao.sp_finalizar_venda
DELIMITER //
CREATE PROCEDURE `sp_finalizar_venda`(
    IN p_id_pedido INT
)
proc:BEGIN

    DECLARE v_total_venda DECIMAL(10,2);
    DECLARE v_total_recebimento DECIMAL(10,2);
    DECLARE v_status CHAR(1);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Erro ao finalizar venda.' AS mensagem;
    END;

    START TRANSACTION;

    SELECT
        PEV_TOTAL,
        PEV_STATUS
    INTO
        v_total_venda,
        v_status
    FROM tb_pedido_venda
    WHERE PEV_ID = p_id_pedido;

    IF v_total_venda IS NULL THEN

        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Venda não encontrada.' AS mensagem;

        LEAVE proc;

    END IF;

    IF v_status = 'F' THEN

        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Venda já faturada.' AS mensagem;

        LEAVE proc;

    END IF;

    IF v_status = 'C' THEN

        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Venda cancelada não pode ser faturada.' AS mensagem;

        LEAVE proc;

    END IF;

    SELECT
        COALESCE(SUM(REC_VALOR),0)
    INTO
        v_total_recebimento
    FROM tb_recebimento
    WHERE REC_VENDA_ID = p_id_pedido
      AND REC_STATUS <> 'C';

    IF v_total_recebimento = 0 THEN

        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Nenhum recebimento informado.' AS mensagem;

        LEAVE proc;

    END IF;

    IF ROUND(v_total_recebimento,2) <> ROUND(v_total_venda,2) THEN

        ROLLBACK;

        SELECT
            0 AS sucesso,
            CONCAT(
                'Total recebido (',
                FORMAT(v_total_recebimento,2),
                ') diferente do total da venda (',
                FORMAT(v_total_venda,2),
                ').'
            ) AS mensagem;

        LEAVE proc;

    END IF;

    /*
        Desconta estoque
    */
    CALL sp_descontar_estoque(p_id_pedido);

    /*
        Fatura venda
    */
    UPDATE tb_pedido_venda
    SET PEV_STATUS = 'F'
    WHERE PEV_ID = p_id_pedido;

    COMMIT;

    SELECT
        1 AS sucesso,
        'Venda faturada com sucesso.' AS mensagem;

END proc//
DELIMITER ;

-- Copiando estrutura para procedure extensao.sp_gravar_recebimento
DELIMITER //
CREATE PROCEDURE `sp_gravar_recebimento`(
    IN p_forma_pagamento INT,
    IN p_quantidade_parcela INT,
    IN p_valor_total DECIMAL(10,2),
    IN p_id_pedido INT
)
proc:BEGIN

    DECLARE v_cliente_id INT;
    DECLARE v_rec_id INT;
    DECLARE v_parcela INT DEFAULT 1;
    DECLARE v_valor_parcela DECIMAL(10,2);
    DECLARE v_valor_ultima_parcela DECIMAL(10,2);
    DECLARE v_observacao VARCHAR(255);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Erro ao gravar recebimento.' AS mensagem;
    END;

    START TRANSACTION;

    SELECT PEV_CLIENTE_ID
    INTO v_cliente_id
    FROM tb_pedido_venda
    WHERE PEV_ID = p_id_pedido;

    IF v_cliente_id IS NULL THEN

        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Pedido não encontrado.' AS mensagem;

        LEAVE proc;

    END IF;

    IF p_forma_pagamento NOT IN (1,2,3,4) THEN

        ROLLBACK;

        SELECT
            0 AS sucesso,
            'Forma de pagamento inválida.' AS mensagem;

        LEAVE proc;

    END IF;

    SET v_observacao =
        CONCAT('Recebimento da venda #', p_id_pedido);

    /*
        DINHEIRO E PIX
    */
    IF p_forma_pagamento IN (1,2) THEN

        IF p_quantidade_parcela > 1 THEN

            ROLLBACK;

            SELECT
                0 AS sucesso,
                'Dinheiro e PIX não permitem parcelamento.' AS mensagem;

            LEAVE proc;

        END IF;

        INSERT INTO tb_recebimento
        (
            REC_VENDA_ID,
            REC_VALOR,
            REC_STATUS,
            REC_VALOR_PAGO,
            REC_OBSERVACAO,
            REC_DEVEDOR_ID,
            REC_DATA_BAIXA,
            REC_VENCIMENTO,
            REC_TIPO_DOCUMENTO_ID
        )
        VALUES
        (
            p_id_pedido,
            p_valor_total,
            'B',
            p_valor_total,
            v_observacao,
            v_cliente_id,
            NOW(),
            CURDATE(),
            p_forma_pagamento
        );

        SET v_rec_id = LAST_INSERT_ID();

        INSERT INTO tb_recebimento_item
        (
            RECI_REC_ID,
            RECI_VALOR,
            RECI_TIPO_DOCUMENTO
        )
        VALUES
        (
            v_rec_id,
            p_valor_total,
            p_forma_pagamento
        );

    ELSE

        /*
            BOLETO E DUPLICATA
        */

        IF p_quantidade_parcela <= 0 THEN

            ROLLBACK;

            SELECT
                0 AS sucesso,
                'Quantidade de parcelas inválida.' AS mensagem;

            LEAVE proc;

        END IF;

        SET v_valor_parcela =
            ROUND(p_valor_total / p_quantidade_parcela, 2);

        SET v_valor_ultima_parcela =
            p_valor_total -
            (v_valor_parcela * (p_quantidade_parcela - 1));

        WHILE v_parcela <= p_quantidade_parcela DO

            INSERT INTO tb_recebimento
            (
                REC_VENDA_ID,
                REC_VALOR,
                REC_STATUS,
                REC_VALOR_PAGO,
                REC_OBSERVACAO,
                REC_DEVEDOR_ID,
                REC_VENCIMENTO,
                REC_TIPO_DOCUMENTO_ID
            )
            VALUES
            (
                p_id_pedido,

                CASE
                    WHEN v_parcela = p_quantidade_parcela
                    THEN v_valor_ultima_parcela
                    ELSE v_valor_parcela
                END,

                'A',

                0,

                CONCAT(
                    v_observacao,
                    ' - Parcela ',
                    v_parcela,
                    '/',
                    p_quantidade_parcela
                ),

                v_cliente_id,

                DATE_ADD(
                    CURDATE(),
                    INTERVAL (v_parcela * 30) DAY
                ),
                p_forma_pagamento
            );

            SET v_parcela = v_parcela + 1;

        END WHILE;

    END IF;

    COMMIT;

    SELECT
        1 AS sucesso,
        'Recebimento gravado com sucesso.' AS mensagem;

END proc//
DELIMITER ;

-- Copiando estrutura para tabela extensao.tb_pagamento
CREATE TABLE IF NOT EXISTS `tb_pagamento` (
  `PAG_ID` int(10) NOT NULL AUTO_INCREMENT,
  `PAG_VALOR` decimal(10,2) unsigned NOT NULL,
  `PAG_CREATED_AT` datetime NOT NULL DEFAULT current_timestamp(),
  `PAG_DESCRICAO` varchar(30) NOT NULL DEFAULT 'Despesa',
  `PAG_DATA_VENCIMENTO` date NOT NULL,
  `PAG_STATUS` char(1) NOT NULL DEFAULT 'A' COMMENT 'A= ABERTO; P =PAGO; C = CANCELADO',
  `PAG_VALOR_PAGO` decimal(10,2) DEFAULT 0.00,
  `PAG_VALOR_ABERTO` decimal(10,2) NOT NULL,
  `PAG_FAVORECIDO_ID` int(11) NOT NULL,
  `PAG_OBSERVACAO` text DEFAULT NULL,
  `PAG_DATA_BAIXA` datetime DEFAULT NULL,
  PRIMARY KEY (`PAG_ID`),
  UNIQUE KEY `REC_ID_UNIQUE` (`PAG_ID`),
  KEY `PAG_FAVORECIDO_idx` (`PAG_FAVORECIDO_ID`),
  CONSTRAINT `PAG_FAVORECIDO` FOREIGN KEY (`PAG_FAVORECIDO_ID`) REFERENCES `tb_pessoa` (`PES_ID`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_pagamento_item
CREATE TABLE IF NOT EXISTS `tb_pagamento_item` (
  `PAGI_ID` int(11) NOT NULL AUTO_INCREMENT,
  `PAGI_PAG_ID` int(11) NOT NULL,
  `PAGI_VALOR` decimal(10,2) unsigned NOT NULL,
  `PAGI_STATUS` char(1) NOT NULL DEFAULT 'P' COMMENT 'P =PAGO; C = CANCELADO',
  `PAGI_CREATED_AT` datetime NOT NULL DEFAULT current_timestamp(),
  `PAGI_TIPO_DOCUMENTO` int(11) NOT NULL,
  `PAGI_OBSERVACAO` text DEFAULT NULL,
  PRIMARY KEY (`PAGI_ID`),
  UNIQUE KEY `RECI_ID_UNIQUE` (`PAGI_ID`),
  KEY `PAGI_TIPO_DOCUMENTO_idx` (`PAGI_TIPO_DOCUMENTO`),
  KEY `PAGI_PAG_ID_idx` (`PAGI_PAG_ID`),
  CONSTRAINT `PAGI_PAG_ID` FOREIGN KEY (`PAGI_PAG_ID`) REFERENCES `tb_pagamento` (`PAG_ID`) ON UPDATE CASCADE,
  CONSTRAINT `PAGI_TIPO_DOCUMENTO` FOREIGN KEY (`PAGI_TIPO_DOCUMENTO`) REFERENCES `tb_tipo_documento` (`TDC_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_pedido_venda
CREATE TABLE IF NOT EXISTS `tb_pedido_venda` (
  `PEV_ID` int(10) NOT NULL AUTO_INCREMENT,
  `PEV_DATA_VENDA` date DEFAULT current_timestamp(),
  `PEV_STATUS` char(1) NOT NULL DEFAULT 'A' COMMENT 'A = ABERTO; F = FATURADA; C = CANCELADA',
  `PEV_ACRESCIMO` decimal(10,2) unsigned DEFAULT 0.00,
  `PEV_DESCONTO` decimal(10,2) unsigned DEFAULT 0.00,
  `PEV_CLIENTE_ID` int(10) NOT NULL,
  `PEV_SUB_TOTAL` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `PEV_TOTAL` decimal(10,2) GENERATED ALWAYS AS (`PEV_SUB_TOTAL` + `PEV_ACRESCIMO` - `PEV_DESCONTO`) VIRTUAL,
  PRIMARY KEY (`PEV_ID`),
  UNIQUE KEY `PEV_ID_UNIQUE` (`PEV_ID`),
  KEY `PEV_CLIENTE_ID_idx` (`PEV_CLIENTE_ID`),
  CONSTRAINT `PEV_CLIENTE_ID` FOREIGN KEY (`PEV_CLIENTE_ID`) REFERENCES `tb_pessoa` (`PES_ID`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=' ';

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_pedido_venda_item
CREATE TABLE IF NOT EXISTS `tb_pedido_venda_item` (
  `PEVI_ID` int(10) NOT NULL AUTO_INCREMENT,
  `PEVI_VENDA_ID` int(10) NOT NULL,
  `PEVI_PRD_ID` int(10) NOT NULL,
  `PEVI_QUANTIDADE` decimal(10,2) unsigned NOT NULL,
  `PEVI_PRECO_UNITARIO` decimal(10,2) unsigned NOT NULL,
  `PEVI_SUBTOTAL` decimal(10,2) GENERATED ALWAYS AS (`PEVI_QUANTIDADE` * `PEVI_PRECO_UNITARIO`) VIRTUAL,
  PRIMARY KEY (`PEVI_ID`),
  UNIQUE KEY `PEVI_ID_UNIQUE` (`PEVI_ID`),
  KEY `PEVI_VENDA_ID_idx` (`PEVI_VENDA_ID`),
  KEY `PEVI_PRD_ID_idx` (`PEVI_PRD_ID`),
  CONSTRAINT `PEVI_PRD_ID` FOREIGN KEY (`PEVI_PRD_ID`) REFERENCES `tb_produto` (`PRD_ID`) ON UPDATE CASCADE,
  CONSTRAINT `PEVI_VENDA_ID` FOREIGN KEY (`PEVI_VENDA_ID`) REFERENCES `tb_pedido_venda` (`PEV_ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_pessoa
CREATE TABLE IF NOT EXISTS `tb_pessoa` (
  `PES_ID` int(10) NOT NULL AUTO_INCREMENT,
  `PES_NOME` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `CPF_CNPJ` varchar(14) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `EMAIL` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `TELEFONE` varchar(15) DEFAULT NULL,
  `TIPO_PESSOA` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'F' COMMENT 'F=Fisica;J=Juridica',
  `CEP` varchar(8) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `ENDERECO` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `NUMERO` varchar(6) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `CIDADE` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `BAIRRO` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `UF` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`PES_ID`),
  UNIQUE KEY `CLI_ID_UNIQUE` (`PES_ID`),
  UNIQUE KEY `CPF_CNPJ` (`CPF_CNPJ`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_produto
CREATE TABLE IF NOT EXISTS `tb_produto` (
  `PRD_ID` int(10) NOT NULL AUTO_INCREMENT,
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
  UNIQUE KEY `PRD_DESCRICAO_UNIQUE` (`PRD_DESCRICAO`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_recebimento
CREATE TABLE IF NOT EXISTS `tb_recebimento` (
  `REC_ID` int(10) NOT NULL AUTO_INCREMENT,
  `REC_VENDA_ID` int(10) DEFAULT NULL,
  `REC_VALOR` decimal(10,2) unsigned NOT NULL,
  `REC_CREATED_AT` datetime NOT NULL DEFAULT current_timestamp(),
  `REC_STATUS` char(1) NOT NULL DEFAULT 'A' COMMENT 'A= ABERTO; B = BAIXADO; C = CANCELADO, PB=PARCIALMENTE BAIXADO',
  `REC_VALOR_PAGO` decimal(10,2) DEFAULT 0.00,
  `REC_VALOR_ABERTO` decimal(10,2) GENERATED ALWAYS AS (`REC_VALOR` - `REC_VALOR_PAGO`) VIRTUAL,
  `REC_OBSERVACAO` text DEFAULT NULL,
  `REC_DEVEDOR_ID` int(10) NOT NULL,
  `REC_DATA_BAIXA` datetime DEFAULT NULL,
  `REC_VENCIMENTO` date DEFAULT NULL,
  `REC_TIPO_DOCUMENTO_ID` int(11) NOT NULL,
  PRIMARY KEY (`REC_ID`),
  UNIQUE KEY `REC_ID_UNIQUE` (`REC_ID`),
  KEY `REC_VENDA_ID_idx` (`REC_VENDA_ID`),
  KEY `REC_DEVEDOR_ID_idx` (`REC_DEVEDOR_ID`),
  KEY `REC_TIPO_DOCUMENTO_ID_idx` (`REC_TIPO_DOCUMENTO_ID`),
  CONSTRAINT `REC_DEVEDOR_ID` FOREIGN KEY (`REC_DEVEDOR_ID`) REFERENCES `tb_pessoa` (`PES_ID`) ON UPDATE CASCADE,
  CONSTRAINT `REC_TIPO_DOCUMENTO_ID` FOREIGN KEY (`REC_TIPO_DOCUMENTO_ID`) REFERENCES `tb_tipo_documento` (`TDC_ID`),
  CONSTRAINT `REC_VENDA_ID` FOREIGN KEY (`REC_VENDA_ID`) REFERENCES `tb_pedido_venda` (`PEV_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_recebimento_item
CREATE TABLE IF NOT EXISTS `tb_recebimento_item` (
  `RECI_ID` int(10) NOT NULL AUTO_INCREMENT,
  `RECI_REC_ID` int(10) NOT NULL,
  `RECI_VALOR` decimal(10,2) unsigned NOT NULL,
  `RECI_CREATED_AT` datetime DEFAULT current_timestamp(),
  `RECI_TIPO_DOCUMENTO` int(10) NOT NULL,
  `RECI_DATA` date DEFAULT current_timestamp(),
  PRIMARY KEY (`RECI_ID`),
  UNIQUE KEY `RECI_ID_UNIQUE` (`RECI_ID`),
  KEY `RECI_REC_ID_idx` (`RECI_REC_ID`),
  KEY `RECI_TIPO_DOCUMENTO_idx` (`RECI_TIPO_DOCUMENTO`),
  CONSTRAINT `RECI_REC_ID` FOREIGN KEY (`RECI_REC_ID`) REFERENCES `tb_recebimento` (`REC_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `RECI_TIPO_DOCUMENTO` FOREIGN KEY (`RECI_TIPO_DOCUMENTO`) REFERENCES `tb_tipo_documento` (`TDC_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_tipo_documento
CREATE TABLE IF NOT EXISTS `tb_tipo_documento` (
  `TDC_ID` int(10) NOT NULL,
  `TDC_DESCRICAO` varchar(45) NOT NULL,
  `TDC_STATUS` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '1 = ATIVO; 0 = INATIVO',
  `TDC_OBSERVACAO` varchar(255) DEFAULT NULL COMMENT 'Informações adicionais sobre o tipo de documento',
  PRIMARY KEY (`TDC_ID`),
  UNIQUE KEY `TDC_ID_UNIQUE` (`TDC_ID`),
  UNIQUE KEY `TDC_DESCRICAO_UNIQUE` (`TDC_DESCRICAO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela extensao.tb_usuario
CREATE TABLE IF NOT EXISTS `tb_usuario` (
  `USU_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `USU_NOME` varchar(45) NOT NULL,
  `USU_LOGIN` varchar(45) NOT NULL,
  `USU_EMAIL` varchar(100) DEFAULT NULL,
  `USU_SENHA` varchar(255) NOT NULL,
  `USU_NIVEL` enum('admin','vendedor','financeiro') NOT NULL DEFAULT 'vendedor',
  `USU_STATUS` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
  `USU_RESET_TOKEN` varchar(255) DEFAULT NULL,
  `USU_RESET_EXPIRA` datetime DEFAULT NULL,
  PRIMARY KEY (`USU_ID`) USING BTREE,
  UNIQUE KEY `USU_ID_UNIQUE` (`USU_ID`) USING BTREE,
  UNIQUE KEY `USU_LOGIN` (`USU_LOGIN`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para trigger extensao.ATUALIZA_VENDA_DELETE_VENDA_ITEM
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER ATUALIZA_VENDA_DELETE_VENDA_ITEM
AFTER DELETE ON TB_PEDIDO_VENDA_ITEM
FOR EACH ROW
BEGIN
    CALL ATUALIZAR_SUB_TOTAL_PEDIDO_VENDA(OLD.PEVI_VENDA_ID);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger extensao.ATUALIZA_VENDA_UPDATE_VENDA_ITEM
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER ATUALIZA_VENDA_UPDATE_VENDA_ITEM
AFTER INSERT ON TB_PEDIDO_VENDA_ITEM
FOR EACH ROW
BEGIN
    CALL ATUALIZAR_SUB_TOTAL_PEDIDO_VENDA(NEW.PEVI_VENDA_ID);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger extensao.trg_pagamento_au
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_pagamento_au
AFTER UPDATE ON tb_pagamento
FOR EACH ROW
BEGIN
    IF OLD.PAG_STATUS <> 'C'
       AND NEW.PAG_STATUS = 'C' THEN


        UPDATE tb_pagamento_item
        SET PAGI_STATUS = 'C'
        WHERE PAGI_PAG_ID = NEW.PAG_ID
          AND PAGI_STATUS <> 'C';


    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger extensao.trg_pagamento_item_ai
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_pagamento_item_ai
AFTER INSERT ON tb_pagamento_item
FOR EACH ROW
BEGIN
    CALL pr_recalcular_pagamento(NEW.PAGI_PAG_ID);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger extensao.trg_pagamento_item_au
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_pagamento_item_au
AFTER UPDATE ON tb_pagamento_item
FOR EACH ROW
BEGIN
    DECLARE v_status CHAR(1);


    IF OLD.PAGI_STATUS <> NEW.PAGI_STATUS THEN


        SELECT PAG_STATUS
        INTO v_status
        FROM tb_pagamento
        WHERE PAG_ID = NEW.PAGI_PAG_ID;


        -- Só recalcula se o pagamento não estiver cancelado
        IF v_status <> 'C' THEN
            CALL pr_recalcular_pagamento(NEW.PAGI_PAG_ID);
        END IF;


    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
