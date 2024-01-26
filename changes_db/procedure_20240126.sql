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

 Date: 26/01/2024 10:18:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
	 
 DECLARE vTotal	DECIMAL DEFAULT 0;
 DECLARE vDepartamentoId	BIGINT DEFAULT 0;
 
 
 IF(pDepartamentoPropio > 0) THEN
   SELECT departamentoId FROM personal WHERE personalId =  pEmpleado INTO vDepartamentoId;
 END IF;
 
 IF (vDepartamentoId > 0) THEN
	SELECT 
				SUM(instanciaServicio.costoWorkflow)
		FROM instanciaServicio 
		WHERE servicioId IN(
			SELECT 
				servicioId
			FROM
				servicio
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
			AND  servicio.status IN ('activo','bajaParcial')
			AND  EXISTS( 
						SELECT tipoServicioId  
						FROM tipoServicio 
						WHERE tipoServicio.tipoServicioId = servicio.tipoServicioId
						AND tipoServicio.departamentoId = vDepartamentoId
			)
		)
		AND year(instanciaServicio.date) = pAnio
		AND MONTH(instanciaServicio.date) = pMes  INTO vTotal;
 ELSE
	 SELECT 
				SUM(instanciaServicio.costoWorkflow)
		FROM instanciaServicio 
		WHERE servicioId IN(
			SELECT 
				servicioId
			FROM
				servicio
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
			AND  servicio.status IN ('activo','bajaParcial')
		)
		AND year(instanciaServicio.date) = pAnio
		AND MONTH(instanciaServicio.date) = pMes  INTO vTotal;
		
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
	 
 DECLARE vTotal	DECIMAL DEFAULT 0;
 DECLARE vDepartamentoId	BIGINT DEFAULT 0;
 
 
 IF(pDepartamentoPropio > 0) THEN
   SELECT departamentoId FROM personal WHERE personalId =  pEmpleado INTO vDepartamentoId;
 END IF;
 
 IF (vDepartamentoId > 0) THEN
	SELECT 
				SUM(instanciaServicio.costoWorkflow)
		FROM instanciaServicio 
		WHERE servicioId IN(
			SELECT 
				servicioId
			FROM
				servicio
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
			AND  servicio.status IN ('activo','bajaParcial')
			AND  EXISTS( 
						SELECT tipoServicioId  
						FROM tipoServicio 
						WHERE tipoServicio.tipoServicioId = servicio.tipoServicioId
						AND tipoServicio.departamentoId = vDepartamentoId
			)
		)
		AND year(instanciaServicio.date) = pAnio
		AND MONTH(instanciaServicio.date) = pMes
		AND instanciaServicio.class = 'Completo' INTO vTotal;
 ELSE
	 SELECT 
				SUM(instanciaServicio.costoWorkflow)
		FROM instanciaServicio 
		WHERE servicioId IN(
			SELECT 
				servicioId
			FROM
				servicio
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
			AND  servicio.status IN ('activo','bajaParcial')
		)
		AND year(instanciaServicio.date) = pAnio
		AND MONTH(instanciaServicio.date) = pMes
	  AND instanciaServicio.class = 'Completo'		INTO vTotal;
		
	END IF;
	
	return vTotal;
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
		
		
		DECLARE cursor_empleados CURSOR FOR SELECT 
		personal.personalId,
		personal.name,
		personal.jefeInmediato,
		(SELECT name from porcentajesBonos WHERE categoria = roles.nivel LIMIT 1) as puesto,
		personal.departamentoId as departamentoId,
		(SELECT departamento from departamentos WHERE departamentoId = personal.departamentoId LIMIT 1) as departamento,
		personal.fechaIngreso
		FROM personal 
		INNER JOIN roles ON personal.roleId = roles.rolId
		WHERE personal.active = '1' and roles.nivel > 1
		ORDER BY roles.nivel ASC;
		
		SELECT COUNT(*)	FROM personal 
		INNER JOIN roles ON personal.roleId = roles.rolId
		WHERE personal.active = '1' and roles.nivel > 1
	  INTO vTotalRow; 
					 
		DROP TEMPORARY TABLE IF EXISTS tmp_personal_bono;
		
		CREATE TEMPORARY TABLE IF NOT EXISTS tmp_personal_bono(
			id bigint,
			nombre VARCHAR(255), 
			jefe bigint, 
			puesto VARCHAR(255),
			departamento VARCHAR(255),
			fecha_ingreso VARCHAR(255) NULL DEFAULT NULL, 
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
			itera_empleado: REPEAT
				FETCH cursor_empleados INTO vPersonalId,VNombre,vJefe,vPuesto,vDepartamentoId,vDepartamento,vFechaIngreso;
				
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
