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

 Date: 31/01/2024 14:31:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Function structure for fn_acumulado_devengado
-- ----------------------------
DROP FUNCTION IF EXISTS `fn_acumulado_devengado`;
delimiter ;;
CREATE FUNCTION `fn_acumulado_devengado`(`pEmpleado` bigint,`pAnio` int,`pMes` int)
 RETURNS decimal(20,2)
  DETERMINISTIC
BEGIN
	#Routine body goes here...
 DECLARE vTotal	DECIMAL(10,2) DEFAULT 0;
 
 DECLARE vCosto	DECIMAL(10,2) DEFAULT 0;
 
 DECLARE vTotalRow BIGINT DEFAULT 0;
 DECLARE vCurrentRow BIGINT DEFAULT 0;

 DECLARE vAcumulado	INT DEFAULT 0;
 DECLARE vAcumularEventual	INT DEFAULT 0;
 DECLARE vAcumularUnicaOcasion	INT DEFAULT 0;
 
 DECLARE cursor_instancias CURSOR FOR 
			SELECT
				instanciaServicio.costoWorkflow costo,
			IF(servicio.`status` = 'bajaParcial',DATE_FORMAT(instanciaServicio.date,'%Y-%m') <= DATE_FORMAT(servicio.lastDateWorkflow,'%Y-%m'),1) acumular,
			IF(servicio.`uniqueInvoice` = 1,DATE_FORMAT(instanciaServicio.date,'%Y-%m') = DATE_FORMAT(servicio.inicioFactura,'%Y-%m'),1) acumularUnicaOcasion,
			IF(servicio.`periodicidad` = 'Eventual',DATE_FORMAT(instanciaServicio.date,'%Y-%m') = DATE_FORMAT(servicio.inicioOperaciones,'%Y-%m'),1) acumularEventual
			FROM instanciaServicio
			INNER JOIN(
				SELECT
						servicio.servicioId,
						servicio.status,
						servicio.lastDateWorkflow,
						servicio.tipoServicioId,
						servicio.inicioOperaciones,
						servicio.inicioFactura,
						tipoServicio.uniqueInvoice,
						tipoServicio.periodicidad
					FROM
						servicio
					INNER JOIN (
						SELECT 
									tipoServicio.tipoServicioId,
									tipoServicio.uniqueInvoice,
									tipoServicio.periodicidad
								FROM tipoServicio 
								INNER JOIN departamentos ON tipoServicio.departamentoId = departamentos.departamentoId
								AND tipoServicio.is_primary = 1
								AND tipoServicio.status = '1'
								AND departamentos.estatus =1
					) tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
					WHERE servicio.contractId IN(
							SELECT contract.contractId  
							FROM contract 
							INNER JOIN customer ON contract.customerId =  customer.customerId 
							WHERE EXISTS(
								SELECT 
								contractPermiso.personalId 
								FROM contractPermiso 
								WHERE 
									contractPermiso.personalId IN (pEmpleado)
								AND contractPermiso.contractId=contract.contractId
							)
							AND customer.active = '1'
							AND contract.activo = 'Si'
					)
					AND  DATE_FORMAT(STR_TO_DATE(CONCAT_WS('-',pAnio,pMes),'%Y-%m'),'%Y-%m') >= DATE_FORMAT(servicio.inicioOperaciones,'%Y-%m')
					AND  servicio.status IN ('activo','bajaParcial')
			)	servicio ON instanciaServicio.servicioId = servicio.servicioId
			AND year(instanciaServicio.date) = pAnio
			AND MONTH(instanciaServicio.date) = pMes
			AND instanciaServicio.status in ('activa','completa')
			HAVING (acumular = 1 and acumularUnicaOcasion = 1 and acumularEventual = 1);
	
	OPEN cursor_instancias;
	select FOUND_ROWS() into vTotalRow;
	
	IF(vTotalRow <= 0) THEN
	 RETURN vTotal;
	END IF; 
	
	itera_instancia: REPEAT
		FETCH cursor_instancias INTO vCosto,vAcumulado,vAcumularUnicaOcasion,vAcumularEventual;
		
		IF(vAcumulado = 1 AND vAcumularUnicaOcasion =1 AND vAcumularEventual = 1) THEN
			SET vTotal = vTotal+vCosto;
		END IF;	
		
	SET vCurrentRow = vCurrentRow+1;
	UNTIL (vCurrentRow >= vTotalRow)
	END REPEAT itera_instancia;		
	CLOSE cursor_instancias;

	RETURN vTotal;
END
;;
delimiter ;

-- ----------------------------
-- Function structure for fn_acumulado_devengado_x_departamento
-- ----------------------------
DROP FUNCTION IF EXISTS `fn_acumulado_devengado_x_departamento`;
delimiter ;;
CREATE FUNCTION `fn_acumulado_devengado_x_departamento`(`pEmpleado` bigint,`pDepartamento` bigint,`pAnio` int,`pMes` int)
 RETURNS decimal(20,2)
  DETERMINISTIC
BEGIN
	#Routine body goes here...
 DECLARE vTotal	DECIMAL(10,2) DEFAULT 0;
 
 DECLARE vCosto	DECIMAL(10,2) DEFAULT 0;
 
 DECLARE vTotalRow BIGINT DEFAULT 0;
 DECLARE vCurrentRow BIGINT DEFAULT 0;

 DECLARE vAcumulado	INT DEFAULT 0;
 DECLARE vAcumularEventual	INT DEFAULT 0;
 DECLARE vAcumularUnicaOcasion	INT DEFAULT 0;
 
 DECLARE cursor_instancias CURSOR FOR 
			SELECT
				instanciaServicio.costoWorkflow costo,
			IF(servicio.`status` = 'bajaParcial',DATE_FORMAT(instanciaServicio.date,'%Y-%m') <= DATE_FORMAT(servicio.lastDateWorkflow,'%Y-%m'),1) acumular,
			IF(servicio.`uniqueInvoice` = 1,DATE_FORMAT(instanciaServicio.date,'%Y-%m') = DATE_FORMAT(servicio.inicioFactura,'%Y-%m'),1) acumularUnicaOcasion,
			IF(servicio.`periodicidad` = 'Eventual',DATE_FORMAT(instanciaServicio.date,'%Y-%m') = DATE_FORMAT(servicio.inicioOperaciones,'%Y-%m'),1) acumularEventual
			FROM instanciaServicio
			INNER JOIN(
				SELECT
						servicio.servicioId,
						servicio.status,
						servicio.lastDateWorkflow,
						servicio.tipoServicioId,
						servicio.inicioOperaciones,
						servicio.inicioFactura,
						tipoServicio.uniqueInvoice,
						tipoServicio.periodicidad
					FROM
						servicio
					INNER JOIN (
						SELECT 
									tipoServicio.tipoServicioId,
									tipoServicio.uniqueInvoice,
									tipoServicio.periodicidad
								FROM tipoServicio 
								INNER JOIN departamentos ON tipoServicio.departamentoId = departamentos.departamentoId
								AND tipoServicio.is_primary = 1
								AND tipoServicio.status = '1'
								AND departamentos.estatus =1
								AND departamentos.departamentoId =pDepartamento
					) tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
					WHERE servicio.contractId IN(
							SELECT contract.contractId  
							FROM contract 
							INNER JOIN customer ON contract.customerId =  customer.customerId 
							WHERE EXISTS(
								SELECT 
								contractPermiso.personalId 
								FROM contractPermiso 
								WHERE 
									contractPermiso.personalId IN (pEmpleado)
								AND contractPermiso.contractId=contract.contractId
							)
							AND customer.active = '1'
							AND contract.activo = 'Si'
					)
					AND  DATE_FORMAT(STR_TO_DATE(CONCAT_WS('-',pAnio,pMes),'%Y-%m'),'%Y-%m') >= DATE_FORMAT(servicio.inicioOperaciones,'%Y-%m')
					AND  servicio.status IN ('activo','bajaParcial')
			)	servicio ON instanciaServicio.servicioId = servicio.servicioId
			AND year(instanciaServicio.date) = pAnio
			AND MONTH(instanciaServicio.date) = pMes
			AND instanciaServicio.status in ('activa','completa')
			HAVING (acumular = 1 and acumularUnicaOcasion = 1 and acumularEventual = 1);
			
	OPEN cursor_instancias;
	select FOUND_ROWS() into vTotalRow;
	
	IF(vTotalRow <= 0) THEN
	 RETURN vTotal;
	END IF;
	
	itera_instancia: REPEAT
		FETCH cursor_instancias INTO vCosto,vAcumulado,vAcumularUnicaOcasion,vAcumularEventual;
		
		IF(vAcumulado = 1 AND vAcumularUnicaOcasion =1 AND vAcumularEventual = 1) THEN
			SET vTotal = vTotal+vCosto;
		END IF;	
		
	SET vCurrentRow = vCurrentRow+1;
	UNTIL (vCurrentRow >= vTotalRow)
	END REPEAT itera_instancia;		
	CLOSE cursor_instancias;

	RETURN vTotal;
END
;;
delimiter ;

-- ----------------------------
-- Function structure for fn_acumulado_trabajado
-- ----------------------------
DROP FUNCTION IF EXISTS `fn_acumulado_trabajado`;
delimiter ;;
CREATE FUNCTION `fn_acumulado_trabajado`(`pEmpleado` bigint,`pAnio` int,`pMes` int)
 RETURNS decimal(20,2)
  DETERMINISTIC
BEGIN
	#Routine body goes here...
 DECLARE vTotal	DECIMAL(10,2) DEFAULT 0;
 
 DECLARE vCosto	DECIMAL(10,2) DEFAULT 0;
 
 DECLARE vTotalRow BIGINT DEFAULT 0;
 DECLARE vCurrentRow BIGINT DEFAULT 0;

 DECLARE vAcumulado	INT DEFAULT 0;
 DECLARE vAcumularEventual	INT DEFAULT 0;
 DECLARE vAcumularUnicaOcasion	INT DEFAULT 0;
 
 DECLARE cursor_instancias CURSOR FOR 
			SELECT
				instanciaServicio.costoWorkflow costo,
			IF(servicio.`status` = 'bajaParcial',DATE_FORMAT(instanciaServicio.date,'%Y-%m') <= DATE_FORMAT(servicio.lastDateWorkflow,'%Y-%m'),1) acumular,
			IF(servicio.`uniqueInvoice` = 1,DATE_FORMAT(instanciaServicio.date,'%Y-%m') = DATE_FORMAT(servicio.inicioFactura,'%Y-%m'),1) acumularUnicaOcasion,
			IF(servicio.`periodicidad` = 'Eventual',DATE_FORMAT(instanciaServicio.date,'%Y-%m') = DATE_FORMAT(servicio.inicioOperaciones,'%Y-%m'),1) acumularEventual
			FROM instanciaServicio
			INNER JOIN(
				SELECT
						servicio.servicioId,
						servicio.status,
						servicio.lastDateWorkflow,
						servicio.tipoServicioId,
						servicio.inicioOperaciones,
						servicio.inicioFactura,
						tipoServicio.uniqueInvoice,
						tipoServicio.periodicidad
					FROM
						servicio
					INNER JOIN (
						SELECT 
									tipoServicio.tipoServicioId,
									tipoServicio.uniqueInvoice,
									tipoServicio.periodicidad
								FROM tipoServicio 
								INNER JOIN departamentos ON tipoServicio.departamentoId = departamentos.departamentoId
								AND tipoServicio.is_primary = 1
								AND tipoServicio.status = '1'
								AND departamentos.estatus =1
					) tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
					WHERE servicio.contractId IN(
							SELECT contract.contractId  
							FROM contract 
							INNER JOIN customer ON contract.customerId =  customer.customerId 
							WHERE EXISTS(
								SELECT 
								contractPermiso.personalId 
								FROM contractPermiso 
								WHERE 
									contractPermiso.personalId IN (pEmpleado)
								AND contractPermiso.contractId=contract.contractId
							)
							AND customer.active = '1'
							AND contract.activo = 'Si'
					)
					AND  DATE_FORMAT(STR_TO_DATE(CONCAT_WS('-',pAnio,pMes),'%Y-%m'),'%Y-%m') >= DATE_FORMAT(servicio.inicioOperaciones,'%Y-%m')
					AND  servicio.status IN ('activo','bajaParcial')
			)	servicio ON instanciaServicio.servicioId = servicio.servicioId
			AND year(instanciaServicio.date) = pAnio
			AND MONTH(instanciaServicio.date) = pMes
			AND instanciaServicio.status in ('activa','completa')
			AND instanciaServicio.class IN ('Completo','CompletoTardio')
			HAVING (acumular = 1 and acumularUnicaOcasion = 1 and acumularEventual = 1);
	
	OPEN cursor_instancias;
	select FOUND_ROWS() into vTotalRow;
	
	IF(vTotalRow <= 0) THEN
	 RETURN vTotal;
	END IF;
	
	itera_instancia: REPEAT
		FETCH cursor_instancias INTO vCosto,vAcumulado,vAcumularUnicaOcasion,vAcumularEventual;
		
		IF(vAcumulado = 1 AND vAcumularUnicaOcasion =1 AND vAcumularEventual = 1) THEN
			SET vTotal = vTotal+vCosto;
		END IF;	
		
	SET vCurrentRow = vCurrentRow+1;
	UNTIL (vCurrentRow >= vTotalRow)
	END REPEAT itera_instancia;		
	CLOSE cursor_instancias;

	RETURN vTotal;
END
;;
delimiter ;

-- ----------------------------
-- Function structure for fn_acumulado_trabajado_x_departamento
-- ----------------------------
DROP FUNCTION IF EXISTS `fn_acumulado_trabajado_x_departamento`;
delimiter ;;
CREATE FUNCTION `fn_acumulado_trabajado_x_departamento`(`pEmpleado` bigint,`pDepartamento` bigint,`pAnio` int,`pMes` int)
 RETURNS decimal(20,2)
  DETERMINISTIC
BEGIN
	#Routine body goes here...
 DECLARE vTotal	DECIMAL(10,2) DEFAULT 0;
 
 DECLARE vCosto	DECIMAL(10,2) DEFAULT 0;
 
 DECLARE vTotalRow BIGINT DEFAULT 0;
 DECLARE vCurrentRow BIGINT DEFAULT 0;

 DECLARE vAcumulado	INT DEFAULT 0;
 DECLARE vAcumularEventual	INT DEFAULT 0;
 DECLARE vAcumularUnicaOcasion	INT DEFAULT 0;
 
 DECLARE cursor_instancias CURSOR FOR 
			SELECT
				instanciaServicio.costoWorkflow costo,
			IF(servicio.`status` = 'bajaParcial',DATE_FORMAT(instanciaServicio.date,'%Y-%m') <= DATE_FORMAT(servicio.lastDateWorkflow,'%Y-%m'),1) acumular,
			IF(servicio.`uniqueInvoice` = 1,DATE_FORMAT(instanciaServicio.date,'%Y-%m') = DATE_FORMAT(servicio.inicioFactura,'%Y-%m'),1) acumularUnicaOcasion,
			IF(servicio.`periodicidad` = 'Eventual',DATE_FORMAT(instanciaServicio.date,'%Y-%m') = DATE_FORMAT(servicio.inicioOperaciones,'%Y-%m'),1) acumularEventual
			FROM instanciaServicio
			INNER JOIN(
				SELECT
						servicio.servicioId,
						servicio.status,
						servicio.lastDateWorkflow,
						servicio.tipoServicioId,
						servicio.inicioOperaciones,
						servicio.inicioFactura,
						tipoServicio.uniqueInvoice,
						tipoServicio.periodicidad
					FROM
						servicio
					INNER JOIN (
						SELECT 
									tipoServicio.tipoServicioId,
									tipoServicio.uniqueInvoice,
									tipoServicio.periodicidad
								FROM tipoServicio 
								INNER JOIN departamentos ON tipoServicio.departamentoId = departamentos.departamentoId
								AND tipoServicio.is_primary = 1
								AND tipoServicio.status = '1'
								AND departamentos.estatus =1
								AND departamentos.departamentoId =pDepartamento
					) tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
					WHERE servicio.contractId IN(
							SELECT contract.contractId  
							FROM contract 
							INNER JOIN customer ON contract.customerId =  customer.customerId 
							WHERE EXISTS(
								SELECT 
								contractPermiso.personalId 
								FROM contractPermiso 
								WHERE 
									contractPermiso.personalId IN (pEmpleado)
								AND contractPermiso.contractId=contract.contractId
							)
							AND customer.active = '1'
							AND contract.activo = 'Si'
					)
					AND  DATE_FORMAT(STR_TO_DATE(CONCAT_WS('-',pAnio,pMes),'%Y-%m'),'%Y-%m') >= DATE_FORMAT(servicio.inicioOperaciones,'%Y-%m')
					AND  servicio.status IN ('activo','bajaParcial')
			)	servicio ON instanciaServicio.servicioId = servicio.servicioId
			AND year(instanciaServicio.date) = pAnio
			AND MONTH(instanciaServicio.date) = pMes
			AND instanciaServicio.status in ('activa','completa')
			AND instanciaServicio.class IN ('Completo','CompletoTardio')
			HAVING (acumular = 1 and acumularUnicaOcasion = 1 and acumularEventual = 1);
			
	OPEN cursor_instancias;
	select FOUND_ROWS() into vTotalRow;
	
	IF(vTotalRow <= 0) THEN
	 RETURN vTotal;
	END IF;
	
	itera_instancia: REPEAT
		FETCH cursor_instancias INTO vCosto,vAcumulado,vAcumularUnicaOcasion,vAcumularEventual;
		
		IF(vAcumulado = 1 AND vAcumularUnicaOcasion =1 AND vAcumularEventual = 1) THEN
			SET vTotal = vTotal+vCosto;
		END IF;	
		
	SET vCurrentRow = vCurrentRow+1;
	UNTIL (vCurrentRow >= vTotalRow)
	END REPEAT itera_instancia;		
	CLOSE cursor_instancias;

	RETURN vTotal;
END
;;
delimiter ;

-- ----------------------------
-- Function structure for get_total_acumulado_devengado
-- ----------------------------
DROP FUNCTION IF EXISTS `get_total_acumulado_devengado`;
delimiter ;;
CREATE FUNCTION `get_total_acumulado_devengado`(`pEmpleado` bigint,`pAnio` int,`pMes` int,`pIncluirSubordinado` int, `pDepartamentoPropio` int)
 RETURNS decimal(20,2)
  DETERMINISTIC
BEGIN
	#Routine body goes here...
 # DEPARTAMENTO AL QUE PERTENECE
	 
 DECLARE vTotal	DECIMAL(10,2) DEFAULT 0;
 DECLARE vAcumulado	DECIMAL(10,2) DEFAULT 0;
 DECLARE vAcumularEventual	DECIMAL(10,2) DEFAULT 0;
 DECLARE vAcumularUnicaOcasion	DECIMAL(10,2) DEFAULT 0;
 DECLARE vDepartamentoId	BIGINT DEFAULT 0;
 
 
 IF(pDepartamentoPropio > 0) THEN
   SELECT departamentoId FROM personal WHERE personalId =  pEmpleado INTO vDepartamentoId;
 END IF;
 
 IF (vDepartamentoId > 0) THEN
	RETURN fn_acumulado_devengado_x_departamento(pEmpleado,vDepartamentoId,pAnio,pMes);
 ELSE
	RETURN fn_acumulado_devengado(pEmpleado,pAnio,pMes);
 END IF;
	
	return vTotal;
END
;;
delimiter ;

-- ----------------------------
-- Function structure for get_total_acumulado_trabajado
-- ----------------------------
DROP FUNCTION IF EXISTS `get_total_acumulado_trabajado`;
delimiter ;;
CREATE FUNCTION `get_total_acumulado_trabajado`(`pEmpleado` bigint,`pAnio` int,`pMes` int,`pIncluirSubordinado` int, `pDepartamentoPropio` int)
 RETURNS decimal(20,2)
  DETERMINISTIC
BEGIN
	#Routine body goes here...
 # DEPARTAMENTO AL QUE PERTENECE
 # Servicios en estatus bajaParcial debe ser acumulado hasta la fecha de su ultimo workflow.
	 
 DECLARE vTotal	DECIMAL(10,2) DEFAULT 0;
 DECLARE vAcumulado	DECIMAL(20,2) DEFAULT 0;
 DECLARE vDepartamentoId	BIGINT DEFAULT 0;
 
 
 IF(pDepartamentoPropio > 0) THEN
   SELECT departamentoId FROM personal WHERE personalId =  pEmpleado INTO vDepartamentoId;
 END IF;
 
 IF (vDepartamentoId > 0) THEN
	RETURN fn_acumulado_trabajado_x_departamento(pEmpleado,vDepartamentoId,pAnio,pMes);
 ELSE
	 RETURN fn_acumulado_trabajado(pEmpleado,vDepartamentoId,pAnio,pMes);
	END IF;

END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for sp_get_acumulado_x_empleado
-- ----------------------------
DROP PROCEDURE IF EXISTS `sp_get_acumulado_x_empleado`;
delimiter ;;
CREATE PROCEDURE `sp_get_acumulado_x_empleado`(IN `pEmpleadoId` INT, IN `pAnio` INT, IN `pDepartamentoPropio` INT)
BEGIN
	
	  DECLARE vTotalRow BIGINT DEFAULT 0;
	  DECLARE vCurrentRow BIGINT DEFAULT 1;
	  DECLARE vPersonalId BIGINT;
	  DECLARE vNombre VARCHAR(255);
	  DECLARE vJefe BIGINT;
	  DECLARE vPuesto VARCHAR(255);
	  DECLARE vDepartamentoId BIGINT;
	  DECLARE vDepartamento VARCHAR(255);
	  DECLARE vFechaIngreso VARCHAR(255);
	  DECLARE vSueldo decimal(20,2);
		DECLARE vPorcentaje decimal(20,2);
	
		
		DECLARE cursor_empleados CURSOR FOR SELECT 
		personal.personalId,
		personal.name,
		personal.jefeInmediato,
		(SELECT name from porcentajesBonos WHERE categoria = roles.nivel LIMIT 1) as puesto,
		personal.departamentoId as departamentoId,
		(SELECT departamento from departamentos WHERE departamentoId = personal.departamentoId LIMIT 1) as departamento,
		personal.fechaIngreso,
		personal.sueldo,
		(SELECT porcentaje from porcentajesBonos WHERE categoria = roles.nivel LIMIT 1) as porcentaje
		FROM personal 
		INNER JOIN roles ON personal.roleId = roles.rolId
		WHERE personal.active = '1' and roles.nivel > 1
		ORDER BY roles.nivel ASC, departamento ASC;
		
		DROP TEMPORARY TABLE IF EXISTS tmp_personal_bono;
		
		CREATE TEMPORARY TABLE IF NOT EXISTS tmp_personal_bono(
			id bigint,
			nombre VARCHAR(255), 
			jefe bigint, 
			puesto VARCHAR(255),
			departamento VARCHAR(255),
			fecha_ingreso VARCHAR(255) NULL DEFAULT NULL, 
			sueldo decimal(20,2) DEFAULT 0, 
			porcentaje decimal(20,2) DEFAULT 0, 
			enero decimal(20,2),
			febrero decimal(20,2),
			marzo decimal(20,2),
			abril decimal(20,2),
			mayo decimal(20,2),
			junio decimal(20,2),
			julio decimal(20,2),
			agosto decimal(20,2),
			septiembre decimal(20,2),
			octubre decimal(20,2),
			noviembre decimal(20,2),
			diciembre decimal(20,2),
			enero_trabajado decimal(20,2),
			febrero_trabajado decimal(20,2),
			marzo_trabajado decimal(20,2),
			abril_trabajado decimal(20,2),
			mayo_trabajado decimal(20,2),
			junio_trabajado decimal(20,2),
			julio_trabajado decimal(20,2),
			agosto_trabajado decimal(20,2),
			septiembre_trabajado decimal(20,2),
			octubre_trabajado decimal(20,2),
			noviembre_trabajado decimal(20,2),
			diciembre_trabajado decimal(20,2)
		);
		
		OPEN cursor_empleados;
		SELECT FOUND_ROWS() INTO vTotalRow; 
		
			itera_empleado: REPEAT
				FETCH cursor_empleados INTO vPersonalId,VNombre,vJefe,vPuesto,vDepartamentoId,vDepartamento,vFechaIngreso,vSueldo,vPorcentaje;
				
				IF (vFechaIngreso = '' && vFechaIngreso = '0000-00-00') THEN
					SET vFechaIngreso =  NULL;
				END IF;
				
				INSERT INTO tmp_personal_bono(
					id,
					nombre,
					jefe,
					puesto,
					departamento,
					fecha_ingreso,
					sueldo,
					porcentaje,
					enero,
					febrero,
					marzo,
					abril,
					mayo,
					junio,
					julio,
					agosto,
					septiembre,
					octubre,
					noviembre,
					diciembre,
					enero_trabajado,
					febrero_trabajado,
					marzo_trabajado,
					abril_trabajado,
					mayo_trabajado,
					junio_trabajado,
					julio_trabajado,
					agosto_trabajado,
					septiembre_trabajado,
					octubre_trabajado,
					noviembre_trabajado,
					diciembre_trabajado
			 )
				VALUES(
					vPersonalId,
					vNombre,
					vJefe,
					vPuesto,
					vDepartamento,
					vFechaIngreso,
					vSueldo,
					vPorcentaje,
					get_total_acumulado_devengado(vPersonalId,pAnio,1,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,2,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,3,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,4,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,5,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,6,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,7,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,8,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,9,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,10,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,11,0,pDepartamentoPropio),
					get_total_acumulado_devengado(vPersonalId,pAnio,12,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,1,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,2,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,3,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,4,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,5,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,6,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,7,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,8,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,9,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,10,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,11,0,pDepartamentoPropio),
					get_total_acumulado_trabajado(vPersonalId,pAnio,12,0,pDepartamentoPropio)
				);
					
				SET vCurrentRow = vCurrentRow+1;
			UNTIL (vCurrentRow >= vTotalRow)
			END REPEAT itera_empleado;		
		CLOSE cursor_empleados;
		select * from tmp_personal_bono;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
