DELIMITER //
DROP PROCEDURE IF EXISTS `sp_actualizar_recotizacion_servicio`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_recotizacion_servicio`(IN `pJsonParam` json, IN pUsuarioId INT, IN pUsuario VARCHAR(255), OUT pDataReturn VARCHAR(255))
BEGIN

	DECLARE vServicioId INT;
	DECLARE vCosto DOUBLE;
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

	DECLARE errno INT;
	DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN
		GET CURRENT DIAGNOSTICS CONDITION 1 errno = MYSQL_ERRNO;
		SET pDataReturn = CONCAT_WS('|', 'ERROR', errno);
ROLLBACK;
END;

	DECLARE EXIT HANDLER FOR SQLWARNING BEGIN
		GET CURRENT DIAGNOSTICS CONDITION 1 errno = MYSQL_ERRNO;
		SET pDataReturn = CONCAT_WS('|', 'ERROR', errno);
ROLLBACK;
END;

START TRANSACTION;

INSERT INTO bitacora_importacion(id_tabla, usuario_realizo)VALUES(1, pUsuario);
SET vIdBitacoraImportacion = LAST_INSERT_ID();

	SET vItems = JSON_LENGTH(pJsonParam);

	WHILE vIndex < vItems DO
		SET vServicioId		= JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].id_servicio')));
		SET vCosto				= JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].costo_nuevo_final')));

SELECT REPLACE(format(a.costo, 2), ',', '') as costo, a.inicioOperaciones, a.inicioFactura,
       a.`status`, a.lastDateWorkflow, b.is_primary, a.servicioId
FROM servicio a
         INNER JOIN tipoServicio b ON a.tipoServicioId = b.tipoServicioId
WHERE servicioId = vServicioId
    INTO vAntesCosto, vAntesFio, vAntesFif, vAntesStatus, vAntesFlw, vIsPrimary, vServicioExist;

IF (vAntesFif <> '' && vAntesFif <> '0000-00-00') THEN
			SET vAntesFif = DATE_FORMAT(STR_TO_DATE(vAntesFif, '%Y-%m-%d'), '%Y-%m-%d');

				IF(!ISNULL(vAntesFif) && YEAR(vAntesFif) < 1989) THEN
					SET vAntesFif = NULL;
END IF;
ELSE
				SET vAntesFif = NULL;
END IF;


			IF (vAntesFlw <> '' && vAntesFlw <> '0000-00-00' && vAntesStatus = 'bajaParcial') THEN
				SET vAntesFlw = DATE_FORMAT(STR_TO_DATE(vAntesFlw, '%Y-%m-%d'), '%Y-%m-%d');

ELSE
				SET vAntesFlw = NULL;
END IF;


		IF (vIsPrimary = 1) THEN

			SET vAntes =  JSON_OBJECT('costo', vAntesCosto);

			SET vDespues =  JSON_OBJECT('costo', vCosto);

			IF vAntes <> vDespues THEN

UPDATE servicio SET
    costo = vCosto
WHERE servicioId = vServicioId;

INSERT INTO bitacora_cambio_servicio(servicio_id, antes, despues, id_bitacora_importacion)
VALUES(vServicioId, vAntes, vDespues, vIdBitacoraImportacion);

INSERT INTO historyChanges(servicioId,inicioOperaciones,costo,status,inicioFactura,lastDateWorkflow,personalId,namePerson)
VALUES(vServicioId,vAntesFio,vCosto,'modificacion',vAntesFif,vAntesFlw,pUsuarioId,pUsuario);

SET vTotalRegistro = vTotalRegistro + 1;
END IF;
END IF;
	  SET vIndex = vIndex + 1;
END WHILE;

	IF(vTotalRegistro > 0) THEN
UPDATE bitacora_importacion SET total_registro =  vTotalRegistro WHERE id= vIdBitacoraImportacion;
ELSE
DELETE FROM bitacora_importacion WHERE id = vIdBitacoraImportacion;
END IF;
COMMIT;
SET pDataReturn = CONCAT_WS('|', vIdBitacoraImportacion, vTotalRegistro);
END//
DELIMITER ;