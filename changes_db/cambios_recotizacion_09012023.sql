delimiter $$

CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `root`@`localhost` SQL SECURITY DEFINER VIEW `huerin`.`vw_contract_customer_servicio` AS SELECT
	`a`.`contractId` AS `contractId`,
	`b`.`NAME` AS `NAME`,
	`b`.`nameContact` AS `nameContact`,
	`a`.`servicioId` AS `servicioId`,
	`c`.`nombreServicio` AS `nombreServicio`,
	`a`.`costo` AS `costo`,
	`a`.`inicioFactura` AS `inicioFactura`,
	`a`.`inicioOperaciones` AS `inicioOperaciones`,
	`a`.`fechaBaja` AS `fechaBaja`,
	`a`.`status` AS `status`,
	`a`.`lastDateCreateWorkflow` AS `lastDateCreateWorkflow`,
	`a`.`lastDateWorkflow` AS `lastDateWorkflow`,
	`b`.`rfc` AS `rfc`,
	`c`.`is_primary` AS `is_primary`,
	`c`.`periodicidad`,
	`c`.`uniqueInvoice`
FROM
	((
			`servicio` `a`
			JOIN `vw_customer_contract_activo` `b` ON ( `a`.`contractId` = `b`.`contractId` ))
	JOIN `tipoServicio` `c` ON ( `a`.`tipoServicioId` = `c`.`tipoServicioId` ))
WHERE
	`a`.`status` IN ( 'activo', 'bajaParcial' )
	AND `c`.`status` = '1'$$


DROP PROCEDURE IF EXISTS `sp_get_data_recotizacion`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_data_recotizacion`(IN `pBitacora` int)
BEGIN

	DECLARE vControl INT DEFAULT 0;
	DECLARE vTotalRow  INT DEFAULT 0;
	DECLARE vCurrentRow INT DEFAULT 1;

	DECLARE vContractId INT;
	DECLARE vServicioId INT;
	DECLARE vName VARCHAR(255);
	DECLARE vRfc VARCHAR(15);
	DECLARE vNameContact VARCHAR(255);
	DECLARE vNombreServicio VARCHAR(255);
	DECLARE vNameResponsable VARCHAR(100);
	DECLARE vEmailResponsable VARCHAR(40);
	DECLARE vCosto DECIMAL(20,2);
	DECLARE vCostoAnterior DECIMAL(20,2);
	DECLARE vCostoActual DECIMAL(20,2);
	DECLARE vAntes JSON;
	DECLARE vDespues JSON;
	DECLARE vFif DATE;
	DECLARE vIsPrimary INT DEFAULT 0;
	DECLARE vUnicaOcasion INT DEFAULT 0;
	DECLARE vPeriodicidad VARCHAR(100) DEFAULT NULL;
	DECLARE vBitacoraExist INT DEFAULT 0;
	DECLARE vAllowInData INT DEFAULT 0;
	DECLARE vFechaImportacion TIMESTAMP DEFAULT NULL;

	DECLARE cursor_servicio CURSOR FOR
	SELECT contractId, servicioId, name, nameContact, nombreServicio,
	costo, inicioFactura, rfc, is_primary, periodicidad
	FROM vw_contract_customer_servicio;

	SELECT COUNT(*) FROM vw_contract_customer_servicio INTO vTotalRow;
	SELECT fecha_registro FROM bitacora_importacion WHERE id = pBitacora INTO vFechaImportacion;


	DROP TEMPORARY TABLE IF EXISTS tmp_data_recotizacion;
	CREATE TEMPORARY TABLE IF NOT EXISTS tmp_data_recotizacion(contractId INT, servicioId INT, `name` VARCHAR(255),
	`nombreServicio` VARCHAR(255), costoAnterior DECIMAL(20,2), costoActual DECIMAL(20,2), rfc VARCHAR(15), nameContact VARCHAR(255), nameResponsable VARCHAR(100), emailResponsable VARCHAR(50));

	OPEN cursor_servicio;
			itera_servicio: REPEAT
			FETCH cursor_servicio INTO vContractId, vServicioId, vName, vNameContact, vNombreServicio, vCosto, vFif, vRfc, vIsPrimary, vPeriodicidad, vUnicaOcasion;
			SET vAntes = null;
			SET vDespues = null;
			SET vNameResponsable = null;
			SET vEmailResponsable = null;
			SET vBitacoraExist = 0;
			SET vAllowInData = 0;


			SELECT per.name, per.email
			FROM (SELECT a.contractId, b.departamento, c.name, c.email
					FROM contractPermiso a
					JOIN departamentos b ON a.departamentoId = b.departamentoId
					JOIN personal c ON a.personalId =  c.personalId) per
			WHERE per.contractId = vContractId AND LOWER(per.departamento) LIKE '%atención al cliente%' INTO vNameResponsable, vEmailResponsable;

			select count(*) FROM
			bitacora_cambio_servicio ca
			INNER JOIN (SELECT sa.contractId, sa.servicioId FROM servicio sa INNER JOIN contract sb ON sa.contractId = sb.contractId) cb
			ON ca.servicio_id = cb.servicioId
			WHERE cb.contractId = vContractId AND ca.id_bitacora_importacion = pBitacora INTO vAllowInData;

			SELECT id, antes, despues FROM bitacora_cambio_servicio WHERE servicio_id= vServicioId  AND id_bitacora_importacion = pBitacora
			INTO vBitacoraExist, vAntes, vDespues;

			IF vBitacoraExist > 0 THEN
				SET vCostoAnterior = JSON_UNQUOTE(JSON_EXTRACT(vAntes,'$.costo'));
				SET vCostoActual 	 = JSON_UNQUOTE(JSON_EXTRACT(vDespues,'$.costo'));
			ELSE
				SET vCostoAnterior = vCosto;
				SET vCostoActual 	 = vCosto;
			END IF;

			IF vCostoActual > 0 && vIsPrimary > 0 && vAllowInData > 0 && vPeriodicidad <>'Eventual' && vUnicaOcasion = 0 THEN
				IF (!ISNULL(WEEK(vFif))) THEN
					INSERT INTO tmp_data_recotizacion(contractId, servicioId, `name`, nombreServicio, costoAnterior, costoActual, rfc, nameContact, nameResponsable, emailResponsable)
					VALUES(vContractId, vServicioId, vName, vNombreServicio, vCostoAnterior, vCostoActual, vRfc, vNameContact, vNameResponsable, vEmailResponsable);
				END IF;
			END IF;

			SET vCurrentRow = vCurrentRow+1;

			UNTIL (vCurrentRow > vTotalRow)
			END REPEAT itera_servicio;
	CLOSE cursor_servicio;

  SELECT contractId, `name`, nameContact, rfc, nameResponsable, emailResponsable,
	CONCAT('[', GROUP_CONCAT(
	JSON_OBJECT('nombreServicio', nombreServicio, 'costoAnterior', costoAnterior, 'costoActual', costoActual)
	)
	,']'
	) as servicios, vFechaImportacion as fecha_importacion from tmp_data_recotizacion GROUP BY contractId;
END $$
	
delimiter ;	
	