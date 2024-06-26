ALTER TABLE `office_resource`
ADD COLUMN `velocidad_procesador` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `procesador`,
ADD COLUMN `tipo_memoria_ram` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `memoria_ram`,
ADD COLUMN `tipo_disco_duro` varchar(10) BINARY CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `disco_duro`,
ADD COLUMN `ubicacion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `no_inventario`;

DELIMITER //
DROP PROCEDURE IF EXISTS `huerin`.`sp_importar_inventario`;

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_importar_inventario`(IN `pJsonParam` json, IN pUsuario VARCHAR(255), OUT pDataReturn VARCHAR(255))
BEGIN
	DECLARE vItems INT;
	DECLARE vTipoRecurso VARCHAR(255);
	DECLARE vTipoDispositivo VARCHAR(255);
	DECLARE vTipoEquipo VARCHAR(255);
	DECLARE vTipoSoftware VARCHAR(255);
	DECLARE vProcesador VARCHAR(255);
	DECLARE vVelocidadProcesador VARCHAR(255);
	DECLARE vMemoriaRam VARCHAR(255);
	DECLARE vTipoDeRam VARCHAR(255);
	DECLARE vDiscoDuro VARCHAR(255);
	DECLARE vTipoDeAlmacenamiento VARCHAR(255);
	DECLARE vNoFisico VARCHAR(255);
	DECLARE vEstatus VARCHAR(255);
	DECLARE vUbicacion VARCHAR(255);
	DECLARE vMarca VARCHAR(255);
	DECLARE vModelo VARCHAR(255);
	DECLARE vNumeroSerie VARCHAR(255);
	DECLARE vFechaCompra VARCHAR(255);
	DECLARE vCostoCompra DECIMAL(11,2);
	DECLARE vNumeroLicencia VARCHAR(255);
	DECLARE vCodigoActivacion VARCHAR(255);
	DECLARE vFechaAlta VARCHAR(255);
	DECLARE vFechaVencimiento VARCHAR(255);
	DECLARE vCostoRecuperacion DECIMAL(11,2);
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

	SET vProcesador = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].procesador')));
	IF vProcesador = 'null' THEN
		SET vProcesador =  null;
END IF;

	SET vVelocidadProcesador = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].velocidad_procesador')));
	IF vVelocidadProcesador = 'null' THEN
		SET vVelocidadProcesador =  null;
END IF;

	SET vMemoriaRam = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].memoria_ram')));
	IF vMemoriaRam = 'null' THEN
		SET vMemoriaRam =  null;
END IF;

	SET vTipoDeRam = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].tipo_de_ram')));
	IF vTipoDeRam = 'null' THEN
		SET vTipoDeRam =  null;
END IF;

	SET vDiscoDuro = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].disco_duro')))  ;
	IF vDiscoDuro = 'null' THEN
		SET vDiscoDuro =  null;
END IF;

	SET vTipoDeAlmacenamiento = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].tipo_de_almacenamiento')))  ;
	IF vTipoDeAlmacenamiento = 'null' THEN
		SET vTipoDeAlmacenamiento =  null;
END IF;

	SET vNoFisico = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].no_fisico')));
	IF vNoFisico = 'null' THEN
		SET vNoFisico =  null;
END IF;

	SET vEstatus = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].estatus')));

	SET vUbicacion = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].ubicacion')));
	IF vUbicacion = 'null' THEN
		SET vUbicacion =  null;
END IF;

	SET vMarca = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].marca')));
	IF vMarca = 'null' THEN
		SET vMarca =  null;
END IF;

	SET vModelo = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].modelo')));
	IF vModelo = 'null' THEN
		SET vModelo =  null;
END IF;

	SET vNumeroSerie = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].nuero_serie'))) ;
	IF vNumeroSerie = 'null' THEN
		SET vNumeroSerie =  null;
END IF;

	SET vFechaCompra = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].fecha_compra')));

	SET vCostoCompra = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].costo_compra')));
	IF vFechaCompra = 'null' THEN
		SET vFechaCompra =  null;
END IF;

	SET vNumeroLicencia = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].numero_licencia')));
	IF vNumeroLicencia = 'null' THEN
		SET vNumeroLicencia =  null;
END IF;

	SET vCodigoActivacion = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].codigo_activacion')));
	IF vCodigoActivacion = 'null' THEN
		SET vCodigoActivacion =  null;
END IF;

	SET vFechaAlta = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].fecha_alta')));

	SET vFechaVencimiento = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].fecha_vencimiento')));

	SET vCostoRecuperacion = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].costo_recuperacion')));
	IF vCostoRecuperacion = 'null' THEN
		SET vCostoRecuperacion =  null;
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
    velocidad_procesador,
    memoria_ram,
    tipo_memoria_ram,
    disco_duro,
    tipo_disco_duro,
    descripcion,
    usuario_alta,
    usuario_modificacion,
    fecha_ultima_modificacion,
    ubicacion,
    status
) VALUES(
            vNoFisico,
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
            vVelocidadProcesador,
            vMemoriaRam,
            vTipoDeRam,
            vDiscoDuro,
            vTipoDeAlmacenamiento,
            vObservaciones,
            pUsuario,
            pUsuario,
            now(),
            vUbicacion,
            vEstatus
        );


SET vTotalRegistro = vTotalRegistro + 1;

		SET vIndex = vIndex + 1;
END WHILE;

COMMIT;
SET pDataReturn = CONCAT_WS('|','OK', vTotalRegistro);
END//

DELIMITER ;