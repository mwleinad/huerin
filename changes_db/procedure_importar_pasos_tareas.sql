/*
 Navicat Premium Data Transfer

 Source Server         : Local
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : huerin

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 16/02/2024 12:08:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP PROCEDURE IF EXISTS `huerin`.`sp_importar_pasos_tareas_servicio`;
delimiter ;;
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

	DECLARE vInicioVigenciaPaso VARCHAR(255);
	DECLARE vFinVigenciaPaso VARCHAR(255);
	DECLARE vInicioVigenciaTarea VARCHAR(255);
	DECLARE vFinVigenciaTarea VARCHAR(255);

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
	SET vInicioVigenciaPaso   = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].inicio_vigencia_paso')));
	SET vFinVigenciaPaso      = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].fin_vigencia_paso')));
	SET vNombreTarea          = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].nombre_de_tarea')));
	SET vDescripcionTarea     = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].descripcion_de_tarea')));
	SET vInicioVigenciaTarea  = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].inicio_vigencia_tarea')));
	SET vFinVigenciaTarea     = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].fin_vigencia_tarea')));
	SET vDocumentoAceptado    = JSON_UNQUOTE(JSON_EXTRACT(pJsonParam, CONCAT('$[', vIndex, '].documentos_aceptados')));


	-- ENCONTRAR Y/O REGISTRAR EL SERVICIO
SELECT departamentoId FROM departamentos where departamento = vDepartamento LIMIT 1 INTO vDepartamentoId;
SELECT tipoServicioId FROM tipoServicio where nombreServicio = vNombreCompletoServicio LIMIT 1 INTO vTipoServicioId;

IF ISNULL(vDescripcionPaso) THEN
		SET vDescripcionPaso = '';
END IF;

	IF ISNULL(vDescripcionTarea) THEN
		SET vDescripcionTarea = '';
END IF;

  SET vInicioVigenciaPaso = STR_TO_DATE(vInicioVigenciaPaso,"%Y-%m-%d");
	IF vInicioVigenciaPaso = '0000-00-00' || ISNULL(vInicioVigenciaPaso) THEN
		SET vInicioVigenciaPaso  = '1990-01-01';
END IF;

	SET vFinVigenciaPaso = STR_TO_DATE(vFinVigenciaPaso,"%Y-%m-%d");
	IF vFinVigenciaPaso = '0000-00-00' THEN
		SET vInicioVigenciaPaso = null;
END IF;

	SET vInicioVigenciaTarea = STR_TO_DATE(vInicioVigenciaTarea,"%Y-%m-%d");
	IF vInicioVigenciaTarea = '0000-00-00' || ISNULL(vInicioVigenciaTarea) THEN
		SET vInicioVigenciaTarea  = '1990-01-01';
END IF;

	SET vFinVigenciaTarea = STR_TO_DATE(vFinVigenciaTarea,"%Y-%m-%d");
	IF vFinVigenciaTarea = '0000-00-00' THEN
		SET vInicioVigenciaTarea = null;
END IF;


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
            vInicioVigenciaPaso,
            vFinVigenciaPaso,
            vNextPositionStep
        );

SET vStepId =  LAST_INSERT_ID();
	 SET vTotalPaso = vTotalPaso + 1;

ELSE

UPDATE step SET
                descripcion=vDescripcionPaso,
                effectiveDate=vInicioVigenciaPaso,
                finalEffectiveDate=vFinVigenciaPaso
WHERE stepId = vStepId;

IF ROW_COUNT() > 0 THEN
			SET vTotalPaso = vTotalPaso + 1;
END IF;

END IF;

	-- ENCONTRAR Y/O REGISTRAR TAREAS
SELECT taskId FROM task where nombreTask = vNombreTarea AND stepId = vStepId LIMIT 1 INTO vTaskId;
SELECT GROUP_CONCAT(extension) FROM mime_types where FIND_IN_SET(name,vDocumentoAceptado) > 0 INTO vExtensiones;

IF ISNULL(vExtensiones) THEN
		SET vExtensiones = '';
END IF;

	IF ISNULL(vTaskId) THEN

SELECT count(*)+1 as siguiente FROM task where stepId = vStepId INTO vNextPositionTask;

#SELECT GROUP_CONCAT(concat_ws(",",extension)) as extensiones FROM mime_types where name like CONCAT("%",vDocumentoAceptado,"%") INTO vExtensiones;

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
            vInicioVigenciaTarea,
            vFinVigenciaTarea
        );

SET vTotalTarea = vTotalTarea + 1;
ELSE

UPDATE task SET
                control=vDescripcionTarea,
                extensiones=vExtensiones,
                effectiveDate=vInicioVigenciaTarea,
                finalEffectiveDate=vFinVigenciaTarea
WHERE taskId = vTaskId;

IF ROW_COUNT() > 0 THEN
			SET vTotalTarea = vTotalTarea + 1;
END IF;

END IF;

	SET vIndex = vIndex + 1;
END WHILE;

COMMIT;
SET pDataReturn = CONCAT_WS('|','OK', vTotalServicio,vTotalPaso,vTotalTarea);
END;
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
