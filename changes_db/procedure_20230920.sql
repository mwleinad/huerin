DELIMITER //
DROP PROCEDURE IF EXISTS `sp_importar_inventario`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_importar_inventario`(IN `pJsonParam` json, IN pUsuario VARCHAR(255), OUT pDataReturn VARCHAR(255))
BEGIN

	DECLARE vItems INT;
	DECLARE vNoInventario VARCHAR(255);
	DECLARE vTipoRecurso VARCHAR(255);
	DECLARE vTipoDispositivo VARCHAR(255);
	DECLARE vTipoEquipo VARCHAR(255);
	DECLARE vTipoSoftware VARCHAR(255);
	DECLARE vNumeroSerie VARCHAR(255);
	DECLARE vNumeroLicencia VARCHAR(255);
	DECLARE vCodigoActivacion VARCHAR(255);
	DECLARE vFechaVencimiento VARCHAR(255);
	DECLARE vFechaAlta VARCHAR(255);
	DECLARE vFechaCompra VARCHAR(255);
	DECLARE vCostoCompra DECIMAL(11,2);
	DECLARE vCostoRecuperacion DECIMAL(11,2);
	DECLARE vMarca VARCHAR(255);
	DECLARE vModelo VARCHAR(255);
	DECLARE vProcesador VARCHAR(255);
	DECLARE vMemoriaRam VARCHAR(255);
	DECLARE vDiscoDuro VARCHAR(255);
	DECLARE vObservaciones TEXT;
	DECLARE vTotalRegistro INT DEFAULT 0;
  DECLARE vIndex INT DEFAULT 0;

	DECLARE errno INT;
	DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN
		GET CURRENT DIAGNOSTICS CONDITION 1 errno = MYSQL_ERRNO;
		SET pDataReturn = CONCAT_WS('|', 'ERROR', errno);
ROLLBACK;
END;

	SET vItems = JSON_LENGTH(pJsonParam);

START TRANSACTION;
WHILE vIndex < vItems DO

		SET vNoInventario = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].no_inventario')));
		IF vNoInventario = 'null' THEN
			SET vNoInventario =  null;
END IF;

		SET vTipoRecurso = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].tipo_recurso')));
		IF vTipoRecurso = 'null' THEN
			SET vTipoRecurso =  null;
END IF;

		SET vTipoDispositivo =JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].tipo_dispositivo')));
		IF vTipoDispositivo = 'null' THEN
			SET vTipoDispositivo =  null;
END IF;

		SET vTipoEquipo = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].tipo_equipo')));
		IF vTipoEquipo = 'null' THEN
			SET vTipoEquipo =  null;
END IF;

		SET vTipoSoftware = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].tipo_software')));
		IF vTipoSoftware = 'null' THEN
			SET vTipoSoftware =  null;
END IF;

		SET vNumeroSerie = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].nmuero_serie'))) ;
		IF vNumeroSerie = 'null' THEN
			SET vNumeroSerie =  null;
END IF;

		SET vNumeroLicencia = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].numero_licencia')));
		IF vNumeroLicencia = 'null' THEN
			SET vNumeroLicencia =  null;
END IF;

		SET vCodigoActivacion = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].codigo_activacion')));
		IF vCodigoActivacion = 'null' THEN
			SET vCodigoActivacion =  null;
END IF;

		SET vFechaVencimiento = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].fecha_vencimiento')));


		SET vFechaAlta = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].fecha_alta')));


		SET vFechaCompra = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].fecha_compra')));


		SET vCostoCompra = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].costo_compra')));
		IF vFechaCompra = 'null' THEN
			SET vFechaCompra =  null;
END IF;

		SET vCostoRecuperacion = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].costo_recuperacion')));
		IF vCostoRecuperacion = 'null' THEN
			SET vCostoRecuperacion =  null;
END IF;

		SET vMarca = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].marca')));
		IF vMarca = 'null' THEN
			SET vMarca =  null;
END IF;

		SET vModelo = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].modelo')));
		IF vModelo = 'null' THEN
			SET vModelo =  null;
END IF;

		SET vProcesador = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].procesador')));
		IF vProcesador = 'null' THEN
			SET vProcesador =  null;
END IF;

	  SET vMemoriaRam = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].memoria_ram')));
		IF vMemoriaRam = 'null' THEN
			SET vMemoriaRam =  null;
END IF;

		SET vDiscoDuro = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].disco_duro')))  ;
		IF vDiscoDuro = 'null' THEN
			SET vDiscoDuro =  null;
END IF;

		SET vObservaciones = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].observaciones')));
		IF vObservaciones = 'null' THEN
			SET vObservaciones =  null;
END IF;


		IF ISNULL(WEEK(vFechaVencimiento)) THEN
			SET vFechaVencimiento =  null;
END IF;

		IF ISNULL(WEEK(vFechaAlta)) THEN
			SET vFechaAlta =  null;
ELSE
		  SET vFechaAlta = CONCAT_WS(" ",vFechaAlta,'00:00:00');
END IF;

		IF ISNULL(WEEK(vFechaCompra)) THEN
			SET vFechaCompra =  null;
END IF;





INSERT INTO office_resource(
    no_inventario,
    tipo_recurso,
    tipo_dispositivo,
    tipo_equipo,
    tipo_software,
    no_serie,
    no_licencia,
    codigo_activacion,
    vencimiento,
    fecha_alta,
    fecha_compra,
    costo_compra,
    costo_recuperacion,
    marca,
    modelo,
    procesador,
    memoria_ram,
    disco_duro,
    descripcion,
    usuario_alta,
    usuario_modificacion,
    fecha_ultima_modificacion,
    status
) VALUES(
            vNoInventario,
            vTipoRecurso,
            vTipoDispositivo,
            vTipoEquipo,
            vTipoSoftware,
            vNumeroSerie,
            vNumeroLicencia,
            vCodigoActivacion,
            vFechaVencimiento,
            vFechaAlta,
            vFechaCompra,
            CAST(vCostoCompra AS DECIMAL(11,2)),
            CAST(vCostoRecuperacion AS DECIMAL(11,2)),
            vMarca,
            vModelo,
            vProcesador,
            vMemoriaRam,
            vDiscoDuro,
            vObservaciones,
            pUsuario,
            pUsuario,
            now(),
            'Activo'
        );


SET vTotalRegistro = vTotalRegistro + 1;

		SET vIndex = vIndex + 1;
END WHILE;

COMMIT;
SET pDataReturn = CONCAT_WS('|','OK', vTotalRegistro);
END//
DELIMITER ;