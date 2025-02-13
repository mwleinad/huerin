DELIMITER //
DROP PROCEDURE IF EXISTS `huerin`.`sp_actualizar_servicio`;

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_servicio`(IN `pJsonParam` json, IN pUsuario VARCHAR(255), OUT pDataReturn VARCHAR(255))
BEGIN

	DECLARE vServicioId INT;
	DECLARE vCosto DOUBLE;
	DECLARE vFif VARCHAR(10);
	DECLARE vFio VARCHAR(10);
	DECLARE vFlw VARCHAR(10);
	DECLARE vStatus VARCHAR(50);
	DECLARE vAntes JSON;
	DECLARE vDespues JSON;
	DECLARE vIndex INT DEFAULT 0;
	DECLARE vTotalRegistro INT DEFAULT 0;
	DECLARE vItems INT DEFAULT 0;
	DECLARE vIdBitacoraImportacion INT;
	DECLARE vJsonValid INT DEFAULT 0;

	DECLARE vAntesCosto DOUBLE;
	DECLARE vAntesFif VARCHAR(10);
	DECLARE vAntesFio VARCHAR(10);
	DECLARE vAntesFlw VARCHAR(10);
	DECLARE vAntesStatus VARCHAR(50);
	DECLARE vIsPrimary INT DEFAULT 0;
	DECLARE vServicioExist INT DEFAULT 0;

INSERT INTO bitacora_importacion(id_tabla, usuario_realizo)VALUES(1, pUsuario);
SET vIdBitacoraImportacion = LAST_INSERT_ID();

	SET vItems = JSON_LENGTH(pJsonParam);

	WHILE vIndex < vItems DO
		SET vServicioId		= JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].id_servicio')));
		SET vCosto				= JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].costo')));
		SET vFif					= JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].inicio_facturacion')));
		SET vFio					= JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].inicio_operacion')));
		SET vFlw					= JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].fecha_ultimo_workflow')));
		SET vStatus			  = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].status')));

SELECT REPLACE(format(a.costo, 2), ',', '') as costo, a.inicioOperaciones, a.inicioFactura,
       a.`status`, a.lastDateWorkflow, b.is_primary, a.servicioId
FROM servicio a
         INNER JOIN tipoServicio b ON a.tipoServicioId = b.tipoServicioId
WHERE servicioId = vServicioId
    INTO vAntesCosto, vAntesFio, vAntesFif, vAntesStatus, vAntesFlw, vIsPrimary, vServicioExist;

IF vServicioExist > 0 THEN
			SET vCosto = REPLACE(format(vCosto, 2), ',', '');

			IF (vIsPrimary = 1) THEN
				IF (vFif <> '' && vFif <> '0000-00-00') THEN
					SET vFif = DATE_FORMAT(STR_TO_DATE(vFif, '%d/%m/%Y'), '%Y-%m-%d');
					IF(!ISNULL(vFif) && YEAR(vFif) < 1989) THEN
						SET vFif = NULL;
END IF;
ELSE
					SET vFif = NULL;
END IF;

				IF (vAntesFif <> '' && vAntesFif <> '0000-00-00') THEN
					SET vAntesFif = DATE_FORMAT(STR_TO_DATE(vAntesFif, '%Y-%m-%d'), '%Y-%m-%d');

					IF(!ISNULL(vAntesFif) && YEAR(vAntesFif) < 1989) THEN
						SET vAntesFif = NULL;
END IF;
ELSE
					SET vAntesFif = NULL;
END IF;
END IF;

			IF (vFio <> '' && vFio <> '0000-00-00') THEN
				SET vFio = DATE_FORMAT(STR_TO_DATE(vFio, '%d/%m/%Y'), '%Y-%m-%d');
ELSE
				SET vFio = NULL;
END IF;

			IF (vFlw <> '' && vFlw <> '0000-00-00' && vStatus = 'bajaParcial') THEN
				SET vFlw = DATE_FORMAT(STR_TO_DATE(vFlw, '%d/%m/%Y'), '%Y-%m-%d');
ELSE
				SET vFlw = NULL;
END IF;


			IF (vAntesFio <> '' && vAntesFio <> '0000-00-00') THEN
				SET vAntesFio = DATE_FORMAT(STR_TO_DATE(vAntesFio, '%Y-%m-%d'), '%Y-%m-%d');
ELSE
				SET vAntesFio = NULL;
END IF;

			IF (vAntesFlw <> '' && vAntesFlw <> '0000-00-00' && vAntesStatus = 'bajaParcial') THEN
				SET vAntesFlw = DATE_FORMAT(STR_TO_DATE(vAntesFlw, '%Y-%m-%d'), '%Y-%m-%d');

ELSE
				SET vAntesFlw = NULL;
END IF;

			IF (vIsPrimary = 1) THEN
				SET vAntes =  JSON_OBJECT('costo', vAntesCosto, 'inicioOperaciones', vAntesFio, 'inicioFactura', vAntesFif,
				'status', vAntesStatus, 'lastDateWorkflow', vAntesFlw);

				SET vDespues =  JSON_OBJECT('costo', vCosto, 'inicioOperaciones', vFio, 'inicioFactura', vFif,
				'status', vStatus, 'lastDateWorkflow', vFlw);
ELSE
				SET vAntes =  JSON_OBJECT('inicioOperaciones', vAntesFio,'status', vAntesStatus, 'lastDateWorkflow', vAntesFlw);

				SET vDespues =  JSON_OBJECT('inicioOperaciones', vFio,'status', vStatus, 'lastDateWorkflow', vFlw);
END IF;

			IF JSON_VALID(vAntes) THEN
				IF vAntes <> vDespues THEN
					IF (vIsPrimary = 1) THEN
UPDATE servicio SET
                    costo = vCosto,
                    inicioOperaciones = vFio,
                    inicioFactura = vFif,
                    lastDateWorkflow = vFlw,
                    lastDateCreateWorkflow= '0000-00-00',
                    `status` = vStatus
WHERE servicioId = vServicioId;
ELSE
UPDATE servicio SET
                    costo = 0,
                    inicioOperaciones = vFio,
                    inicioFactura = null,
                    lastDateWorkflow = vFlw,
                    lastDateCreateWorkflow= '0000-00-00',
                    `status` = vStatus
WHERE servicioId = vServicioId;
END IF;

INSERT INTO bitacora_cambio_servicio(servicio_id, antes, despues, id_bitacora_importacion)
VALUES(vServicioId, vAntes, vDespues, vIdBitacoraImportacion);

SET vTotalRegistro = vTotalRegistro + 1;
END IF;
END IF;
END IF;
	  SET vIndex = vIndex + 1;
END WHILE;

	IF(vTotalRegistro > 0) THEN
UPDATE bitacora_importacion SET total_registro =  vTotalRegistro WHERE id= vIdBitacoraImportacion;
ELSE
DELETE FROM bitacora_importacion WHERE id = vIdBitacoraImportacion;
END IF;

	SET pDataReturn = CONCAT_WS('|', vIdBitacoraImportacion, vTotalRegistro);
END//
DELIMITER ;