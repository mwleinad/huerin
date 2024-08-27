DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_importar_pasos_tareas_servicio`(IN `pJsonParam` json, IN pUsuario VARCHAR(255), OUT pDataReturn VARCHAR(255))
BEGIN

	DECLARE vTotalServicio INT DEFAULT 0;
	DECLARE vTotalPaso INT DEFAULT 0;
	DECLARE vTotalTarea INT DEFAULT 0;
  DECLARE vIndex INT DEFAULT 0;
	
	DECLARE vDepartamento VARCHAR(255);
	DECLARE vNomenclaturaServicio VARCHAR(255);
	DECLARE vNombreServicio VARCHAR(255);
	DECLARE vNombreCompletoServicio VARCHAR(255);
	DECLARE vNombrePaso VARCHAR(255);
	DECLARE vDescripcionPaso VARCHAR(255);
	DECLARE vNombreTarea VARCHAR(255);
	DECLARE vDescripcionTarea VARCHAR(255);
	DECLARE vDocumentoAceptado VARCHAR(255);
	DECLARE vExtensiones VARCHAR(255);

  DECLARE vDepartamentoId INT;
  DECLARE vTipoServicioId INT;
  DECLARE vStepId INT;
  DECLARE vTaskId INT;
  DECLARE vNextPositionStep INT;
  DECLARE vNextPositionTask INT;

	DECLARE vSeguimiento VARCHAR(255) DEFAULT 'INICIO';

	DECLARE vItems INT;
	DECLARE errno INT;

	DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
		GET CURRENT DIAGNOSTICS CONDITION 1 errno = MYSQL_ERRNO;
		SET pDataReturn = CONCAT_WS('|', 'ERROR', errno);
ROLLBACK;
END;

	SET vItems = JSON_LENGTH(pJsonParam);



START TRANSACTION;

WHILE vIndex < vItems DO


	SET vDepartamento         = '';
	SET vNomenclaturaServicio = '';
	SET vNombreServicio       = '';
	SET vNombreCompletoServicio = '';
	SET vNombrePaso           = '';
	SET vDescripcionPaso      = '';
	SET vNombreTarea          = '';
	SET vDescripcionTarea     = '';
	SET vDocumentoAceptado    = '';

	SET vDepartamentoId    = null;
	SET vTipoServicioId    = null;
	SET vStepId    = null;
	SET vTaskId    = null;



	SET vDepartamento         = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].area')));
	SET vNomenclaturaServicio = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].nomenclatura_de_servicio')));
	SET vNombreServicio       = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].servicio')));
	SET vNombreCompletoServicio = CONCAT_WS(" ",vNomenclaturaServicio,vNombreServicio);
	SET vNombrePaso           = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].nombre_de_paso')));
	SET vDescripcionPaso      = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].descripcion_de_paso')));
	SET vNombreTarea          = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].nombre_de_tarea')));
	SET vDescripcionTarea     = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].descripcion_de_tarea')));
	SET vDocumentoAceptado    = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].documento_aceptado')));


	-- ENCONTRAR Y/O REGISTRAR EL SERVICIO
SELECT departamentoId FROM departamentos where departamento = vDepartamento LIMIT 1 INTO vDepartamentoId;
SELECT tipoServicioId FROM tipoServicio where nombreServicio = vNombreCompletoServicio LIMIT 1 INTO vTipoServicioId;


IF ISNULL(vTipoServicioId) THEN


		 INSERT INTO tipoServicio(
		 nombreServicio,
		 claveSat,
		 costo,
		 periodicidad,
		 costoUnico,
		 maxDay,
		 departamentoId,
		 costoVisual,
		 mostrarCostoVisual,
		 uniqueInvoice,
		 is_primary,
		 concepto_mes_vencido)
		 VALUES(
		  vNombreCompletoServicio,
		  '84111505',
			0,
			'Mensual',
			0,
			17,
			vDepartamentoId,
			'0',
			1,
			0,
			1,
			0
		 );
		 SET vTipoServicioId = LAST_INSERT_ID();
		 SET vTotalServicio = vTotalServicio + 1;

END IF;


	-- ENCONTRAR Y/O REGISTRAR PASOS
SELECT stepId FROM step where nombreStep = vNombrePaso AND servicioId = vTipoServicioId LIMIT 1 INTO vStepId;

IF ISNULL(vStepId)  THEN

SELECT count(*)+1 as siguiente FROM step where servicioId = vTipoServicioId INTO vNextPositionStep;

INSERT INTO step (
    servicioId,
    nombreStep,
    descripcion,
    effectiveDate,
    finalEffectiveDate,
    position
) VALUES(
            vTipoServicioId,
            vNombrePaso,
            vDescripcionPaso,
            '1990-01-01',
            null,
            vNextPositionStep
        );

SET vStepId =  LAST_INSERT_ID();
	 SET vTotalPaso = vTotalPaso + 1;
END IF;

	-- ENCONTRAR Y/O REGISTRAR TAREAS
SELECT taskId FROM task where nombreTask = vNombreTarea AND stepId = vStepId LIMIT 1 INTO vTaskId;

IF ISNULL(vTaskId) THEN

SELECT count(*)+1 as siguiente FROM task where stepId = vStepId INTO vNextPositionTask;
SELECT GROUP_CONCAT(concat_ws(",",extension)) as extensiones FROM mime_types where name like CONCAT("%",vDocumentoAceptado,"%") INTO vExtensiones;

INSERT INTO task(
    stepId,
    nombreTask,
    diaVencimiento,
    prorroga,
    control,
    control2,
    control3,
    extensiones,
    taskPosition,
    effectiveDate,
    finalEffectiveDate
) VALUES(
            vStepId,
            vNombreTarea,
            5,
            0,
            vDescripcionTarea,
            '',
            '',
            vExtensiones,
            vNextPositionTask,
            '1990-01-01',
            null
        );

SET vTotalTarea = vTotalTarea + 1;
END IF;

	SET vIndex = vIndex + 1;
END WHILE;

COMMIT;
SET pDataReturn = CONCAT_WS('|','OK', vTotalServicio,vTotalPaso,vTotalTarea);
END//
DELIMITER ;