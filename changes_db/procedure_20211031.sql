DELIMITER //
DROP PROCEDURE IF EXISTS `sp_emp_comp_por_mes`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_emp_comp_por_mes`(
	IN `pPersonalId` INT,
	IN `pMesInicio` INT,
	IN `pMesFin` INT,
	IN `pAnio` INT
)
LANGUAGE SQL
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT 'Lista a las empresas con la suma de sus comprobantes agrupados por mes , dado un responsable'
BEGIN
	DECLARE vContractId INT (11);
	DECLARE control INT DEFAULT 0;
	DECLARE vCustomerName VARCHAR (255);
	DECLARE vContractIdTemp INT (11);
	DECLARE vNameTemp VARCHAR(255);
	DECLARE vFacturaTemp JSON;
	DECLARE vTotalRow INT DEFAULT 0;
	DECLARE vTotalFoundRow INT DEFAULT 0;
	DECLARE vCurrentRow INT DEFAULT 0;
	DECLARE debug_string  TEXT;
	DECLARE cursor_contract CURSOR FOR	SELECT a.contractId, c.nameContact AS customer_name FROM contract a
	                                    INNER JOIN contractPermiso b ON a.contractId = b.contractId                                                                                                                                                                                 INNER JOIN customer c ON a.customerId = c.customerId
                                        WHERE b.personalId = pPersonalId AND activo='Si';
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET control = 1;

SELECT COUNT(*) FROM contract a
                         INNER JOIN contractPermiso b ON a.contractId = b.contractId
                         INNER JOIN customer c ON a.customerId = c.customerId
WHERE b.personalId = pPersonalId AND activo='Si' INTO vTotalRow;

DROP TEMPORARY TABLE IF EXISTS tmp_contract_comprobante;
	CREATE TEMPORARY TABLE IF NOT EXISTS tmp_contract_comprobante(contract_id INT PRIMARY KEY, user_id INT, customer_name VARCHAR(255), name VARCHAR(255), factura JSON);

OPEN cursor_contract;
SET vCurrentRow = 1;
	itera_contract: REPEAT
		fetch cursor_contract INTO vContractId, vCustomerName;
SELECT userId, name_company, CONCAT('[',
                                    GROUP_CONCAT(
                                            JSON_OBJECT(
                                                    'contract_id',userId,
                                                    'total', total,
                                                    'abono', abono,
                                                    'saldo', round(total - abono, 2),
                                                    'mes', mes)), ']') AS factura
FROM (
         SELECT a.userId, c.name AS name_company, SUM(a.total) AS total, IF(SUM(b.amount) IS NULL,0,SUM(b.amount)) AS abono, MONTH(a.fecha) AS mes
         FROM comprobante a
             LEFT JOIN (SELECT comprobanteId, SUM(amount) AS amount FROM payment WHERE paymentStatus='activo'
             GROUP BY comprobanteId) b ON a.comprobanteId = b.comprobanteId
             INNER JOIN contract c ON a.userId = c.contractId
         WHERE MONTH(a.fecha) >= pMesInicio AND MONTH(a.fecha) <= pMesFin AND YEAR(a.fecha) = pAnio
           AND a.userId = vContractId AND a.tiposComprobanteId = 1 AND a.status = 1
         GROUP BY a.userId, MONTH(a.fecha)
     ) AS tbl_ms
GROUP BY userId INTO vContractIdTemp, vNameTemp, vFacturaTemp;
SET vTotalFoundRow = FOUND_ROWS();
		IF vTotalFoundRow > 0 THEN
			INSERT INTO tmp_contract_comprobante(contract_id, user_id, customer_name, name, factura)VALUES(vContractId, vContractIdTemp, vCustomerName, vNameTemp, vFacturaTemp);
END IF;
		SET vCurrentRow = (vCurrentRow+1);
	UNTIL (vCurrentRow >= vTotalRow)
END REPEAT itera_contract;
CLOSE cursor_contract;
SELECT * FROM tmp_contract_comprobante;
END//
DELIMITER ;