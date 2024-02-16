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

-- ----------------------------
-- Procedure structure for sp_get_empleados
-- ----------------------------
DROP PROCEDURE IF EXISTS `sp_get_empleados`;
delimiter ;;
CREATE PROCEDURE `sp_get_empleados`()
BEGIN
	
	  DECLARE vTotalRow BIGINT DEFAULT 0;
	  DECLARE vCurrentRow BIGINT DEFAULT 0;
	  DECLARE vPersonalId BIGINT;
	  DECLARE vNombre VARCHAR(255);
	  DECLARE vEmail VARCHAR(255);
	  DECLARE vTelefono VARCHAR(255);
	  DECLARE vExtension VARCHAR(255);
	  DECLARE vJefe BIGINT;
	  DECLARE vPuesto VARCHAR(255);
	  DECLARE vAreaId BIGINT;
	  DECLARE vArea VARCHAR(255);
	  DECLARE vDepartamento VARCHAR(255);
	  DECLARE vFechaIngreso VARCHAR(255);
	  DECLARE vSueldo decimal(20,2);
		DECLARE vPorcentaje decimal(20,2);
		DECLARE vMontoBonoEfectivo decimal(20,2);
		DECLARE vMailGrupo VARCHAR(255);
		DECLARE vListaDistribucion VARCHAR(255);
		DECLARE vCuentaInhouse VARCHAR(255);
		DECLARE vInhouse VARCHAR(2);
		DECLARE vEstatus CHAR(1);
		DECLARE vActivo VARCHAR(2);
		
		
	
		
		DECLARE cursor_empleados CURSOR FOR SELECT 
		personal.personalId,
		personal.name,
		personal.email,
		personal.phone,
		personal.ext,
		personal.jefeInmediato,
		(SELECT name from porcentajesBonos WHERE categoria = roles.nivel LIMIT 1) as puesto,
		personal.departamentoId as area_id,
		(SELECT departamento from departamentos WHERE departamentoId = personal.departamentoId LIMIT 1) as area,
		personal.grupo departamento,
		personal.fechaIngreso,
		personal.sueldo,
		(SELECT porcentaje from porcentajesBonos WHERE categoria = roles.nivel LIMIT 1) as porcentaje,
		(SELECT monto from porcentajesBonos WHERE categoria = roles.nivel LIMIT 1) as monto_bono_efectivo,
		personal.mailGrupo,
		personal.listaDistribucion,
		personal.cuentaInhouse,
		personal.active
		FROM personal 
		INNER JOIN roles ON personal.roleId = roles.rolId
		WHERE roles.nivel > 1
        ORDER BY roles.nivel ASC, area ASC ,departamento ASC, personal.name ASC;
		
		DROP TEMPORARY TABLE IF EXISTS tmp_empleados;
		
		CREATE TEMPORARY TABLE IF NOT EXISTS tmp_empleados(
			id bigint,
			nombre VARCHAR(255), 
			email VARCHAR(255), 
			telefono VARCHAR(255), 
			extension VARCHAR(255), 
			jefe bigint default 0, 
			puesto VARCHAR(255),
			area_id bigint,
			area VARCHAR(255),
			departamento VARCHAR(255),
			fecha_ingreso VARCHAR(255) NULL DEFAULT NULL, 
			sueldo decimal(20,2) DEFAULT 0, 
			porcentaje decimal(20,2) DEFAULT 0, 
			monto_bono_efectivo decimal(20,2) DEFAULT 0,
			mail_grupo VARCHAR(255) NULL DEFAULT NULL, 
			lista_distribucion VARCHAR(255) NULL DEFAULT NULL, 
			cuenta_inhouse VARCHAR(255) NULL DEFAULT NULL,
			inhouse VARCHAR(2) DEFAULT 'No',
			activo VARCHAR(2) DEFAULT 'Si'
		);
		
		OPEN cursor_empleados;
		SELECT FOUND_ROWS() INTO vTotalRow; 
		
			itera_empleado: REPEAT
				FETCH cursor_empleados INTO vPersonalId,VNombre,vEmail,vTelefono,vExtension,vJefe,vPuesto,vAreaId,vArea,vDepartamento,vFechaIngreso,vSueldo,vPorcentaje,vMontoBonoEfectivo,vMailGrupo,vListaDistribucion,vCuentaInhouse,vEstatus;
				
				IF (vFechaIngreso = '' && vFechaIngreso = '0000-00-00') THEN
					SET vFechaIngreso =  NULL;
				END IF;
				
				IF(CHAR_LENGTH(vCuentaInhouse) > 0) THEN 
					SET vInhouse = 'Si';
				ELSE
					SET vInhouse = 'No';
				END IF;
				
				IF(vEstatus = '1') THEN 
					SET vActivo = 'Si';
				ELSE
					SET vActivo = 'No';
				END IF;		
				
				INSERT INTO tmp_empleados(
					id,
					nombre,
					email,
					telefono,
					extension,
					jefe,
					puesto,
					area_id,
					area,
					departamento,
					fecha_ingreso,
					sueldo,
					porcentaje,
					monto_bono_efectivo,
					mail_grupo,
					lista_distribucion,
					cuenta_inhouse,
					inhouse,
					activo
			 )
				VALUES(
					vPersonalId,
					vNombre,
					vEmail,
					vTelefono,
					vExtension,
					vJefe,
					vPuesto,
					vAreaId,
					vArea,
					vDepartamento,
					vFechaIngreso,
					vSueldo,
					vPorcentaje,
					vMontoBonoEfectivo,
					vMailGrupo,
					vListaDistribucion,
					vCuentaInhouse,
					vInhouse,
					vActivo
				);
					
				SET vCurrentRow = vCurrentRow+1;
			UNTIL (vCurrentRow >= vTotalRow)
			END REPEAT itera_empleado;		
		CLOSE cursor_empleados;
		select * from tmp_empleados;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
