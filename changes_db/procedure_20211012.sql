DELIMETER //
REATE DEFINER=`root`@`localhost` PROCEDURE `sp_verify_secondary`(
	IN `contract_id` INT,
	IN `tipo_servicio_id` INT,
	IN `param_month` INT,
	IN `param_year` VARCHAR(255),
	IN `name_view` VARCHAR(200),
	IN `prospect_database` VARCHAR(255)
)
LANGUAGE SQL
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT 'procedimiento para comprobar que los servicios secundarios de un primario esten realizadas'
BEGIN
	SET @param_view=name_view;
	SET @tipo_id=tipo_servicio_id;
	SET @contract_id=contract_id;
	SET @param_month = param_month;
	SET @param_year = param_year;
	SET @prospect_database = prospect_database;

	DROP TEMPORARY TABLE IF EXISTS tbl_secondary_pendiente;
	DROP TEMPORARY TABLE IF EXISTS tmp_service_secondary;

	SET @query_service = CONCAT("CREATE TEMPORARY TABLE tmp_service_secondary SELECT servicioId from servicio ",
							   " where tipoServicioId IN (select secondary_id from ", @prospect_database, ".secondary_service where service_id=", @tipo_id, ")",
								" and status in('activo', 'bajaParcial') and contractId=", @contract_id);
    PREPARE stmt_service FROM @query_service;
    EXECUTE stmt_service;
    DROP PREPARE stmt_service;

	SET @query_pending = CONCAT("CREATE TEMPORARY TABLE tbl_secondary_pendiente SELECT instancia_id, status, class from ", @param_view,
	" where class NOT IN('CompletoTardio', 'Completo') and servicio_id in (select servicioId from tmp_service_secondary) and month(fecha)=", @param_month, " and year(fecha)=", @param_year);
    PREPARE stmt_pending FROM @query_pending;
    EXECUTE stmt_pending;
    DROP PREPARE stmt_pending;
    SELECT COUNT(*)  from tbl_secondary_pendiente;
END//
DELIMITER ;