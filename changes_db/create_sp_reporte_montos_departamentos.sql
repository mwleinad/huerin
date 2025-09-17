DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_reporte_montos_por_departamento`$$

CREATE PROCEDURE `sp_reporte_montos_por_departamento`(
    IN p_anio INT,
    IN p_mes INT,
    IN p_personal_id INT,
    IN p_departamento_id INT,
    IN p_cliente_nombre VARCHAR(255),
    IN p_empresa_nombre VARCHAR(255)
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE subordinados_list TEXT DEFAULT '';
    DECLARE sql_query TEXT DEFAULT '';
    DECLARE departamento_columns TEXT DEFAULT '';
    DECLARE done_cursor INT DEFAULT FALSE;
    DECLARE v_departamentoId INT;
    DECLARE v_departamento VARCHAR(255);
    
    -- Declarar cursor después de todas las variables
    DECLARE cur_departamentos CURSOR FOR 
        SELECT departamentoId, departamento FROM temp_departamentos ORDER BY departamento;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_cursor = TRUE;
    
    -- Si se especifica un personal, obtener todos sus subordinados
    IF p_personal_id IS NOT NULL THEN
        -- Inicializar con el personal principal
        SET subordinados_list = CONCAT(p_personal_id, ',');
        
        -- Variables para el bucle recursivo
        SET @nivel = 0;
        SET @nuevos_encontrados = 1;
        
        -- Bucle para encontrar subordinados recursivamente
        WHILE @nuevos_encontrados > 0 AND @nivel < 10 DO
            SET @nuevos_encontrados = 0;
            SET @query = CONCAT('SELECT GROUP_CONCAT(personalId) INTO @nuevos_subordinados FROM personal WHERE jefeInmediato IN (', TRIM(TRAILING ',' FROM subordinados_list), ') AND FIND_IN_SET(personalId, "', subordinados_list, '") = 0');
            
            PREPARE stmt FROM @query;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
            
            IF @nuevos_subordinados IS NOT NULL AND @nuevos_subordinados != '' THEN
                SET subordinados_list = CONCAT(subordinados_list, @nuevos_subordinados, ',');
                SET @nuevos_encontrados = 1;
            END IF;
            
            SET @nivel = @nivel + 1;
        END WHILE;
        
        -- Limpiar la última coma
        SET subordinados_list = TRIM(TRAILING ',' FROM subordinados_list);
    END IF;

    DROP TEMPORARY TABLE IF EXISTS temp_departamentos;
    DROP TEMPORARY TABLE IF EXISTS temp_servicio_costos;
    DROP TEMPORARY TABLE IF EXISTS temp_costos_agrupados;

    -- Crear tabla temporal para almacenar departamentos únicos
    CREATE TEMPORARY TABLE temp_departamentos (
        departamentoId INT,
        departamento VARCHAR(255),
        INDEX idx_dept (departamentoId)
    );
    
    -- Obtener todos los departamentos existentes (tengan o no servicios)
    INSERT INTO temp_departamentos (departamentoId, departamento)
    SELECT DISTINCT d.departamentoId, 
           LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(d.departamento,
           'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 
           'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 
           'ñ', 'n'), 'Ñ', 'N'), 'ü', 'u'), 'Ü', 'U'), 
           'ç', 'c'), 'Ç', 'C'), 'à', 'a'), 'è', 'e'), 'ì', 'i'), 'ò', 'o'), 'ù', 'u'), 
           'À', 'A'), 'È', 'E'), 'Ì', 'I'), 'Ò', 'O'), 'Ù', 'U'), 'â', 'a'), 'ê', 'e')) AS departamento
    FROM departamentos d
    WHERE (p_departamento_id IS NULL OR d.departamentoId = p_departamento_id)
    ORDER BY d.departamento;
    
    -- Step 1: Pre-calculate the total costs per service, per year/month
    CREATE TEMPORARY TABLE temp_servicio_costos AS
    SELECT
        s.contractId,
        s.tipoServicioId,
        ts.departamentoId,
        ts.is_primary,
        CASE
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND 
                 (p_mes IS NULL OR MONTH(s.lastDateWorkflow) >= p_mes) THEN
                CASE 
                    WHEN p_mes IS NULL THEN
                        -- Todo el año hasta lastDateWorkflow
                        (SELECT SUM(ins.costoWorkflow) FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio 
                         AND MONTH(ins.date) <= MONTH(s.lastDateWorkflow))
                    ELSE
                        -- Desde enero hasta el mes indicado, pero respetando lastDateWorkflow
                        (SELECT SUM(ins.costoWorkflow) FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio 
                         AND MONTH(ins.date) >= 1 
                         AND MONTH(ins.date) <= LEAST(p_mes, MONTH(s.lastDateWorkflow)))
                END
            WHEN s.status != 'bajaParcial' THEN
                CASE 
                    WHEN p_mes IS NULL THEN
                        -- Todo el año
                        (SELECT SUM(ins.costoWorkflow) FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio)
                    ELSE
                        -- Desde enero hasta el mes indicado
                        (SELECT SUM(ins.costoWorkflow) FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio 
                         AND MONTH(ins.date) >= 1 
                         AND MONTH(ins.date) <= p_mes)
                END
            ELSE 0
        END AS costo_total
    FROM servicio s
    INNER JOIN tipoServicio ts ON s.tipoServicioId = ts.tipoServicioId
    WHERE s.status IN ('activo', 'bajaParcial') AND ts.is_primary = 1;
    
    -- Create aggregated table to avoid multiple references
    CREATE TEMPORARY TABLE temp_costos_agrupados AS
    SELECT 
        contractId,
        departamentoId,
        SUM(costo_total) as total_departamento
    FROM temp_servicio_costos 
    GROUP BY contractId, departamentoId;
    
    -- Step 2: Build the dynamic columns using cursor to avoid GROUP_CONCAT limits
    -- Aumentar el límite de GROUP_CONCAT temporalmente
    SET SESSION group_concat_max_len = 1000000;
    
    -- Inicializar columnas dinámicas
    SET departamento_columns = '';
    
    OPEN cur_departamentos;
    read_loop: LOOP
        FETCH cur_departamentos INTO v_departamentoId, v_departamento;
        IF done_cursor THEN
            LEAVE read_loop;
        END IF;
        
        -- Agregar columna para este departamento
        IF departamento_columns != '' THEN
            SET departamento_columns = CONCAT(departamento_columns, ', ');
        END IF;
        
        SET departamento_columns = CONCAT(departamento_columns, 
            'COALESCE(SUM(CASE WHEN tca.departamentoId = ', v_departamentoId, 
            ' THEN tca.total_departamento ELSE 0 END), 0) AS `', REPLACE(REPLACE(v_departamento, ' ', '_'), '-', '_'), '`');
            
    END LOOP;
    CLOSE cur_departamentos;
    
    -- Si no hay departamentos, establecer columna por defecto
    IF departamento_columns IS NULL OR departamento_columns = '' THEN
        SET departamento_columns = '0 AS Ningun_Departamento';
    END IF;
    
    -- Construir la consulta dinámica
    SET sql_query = CONCAT('
        SELECT 
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                REPLACE(
                                                    REPLACE(
                                                        REPLACE(
                                                            TRIM(REGEXP_REPLACE(c.nameContact, "\\\\s{2,}", " ")),
                                                            "LE?N", "LEÓN"
                                                        ),
                                                        "NU?EZ", "NUÑEZ"
                                                    ),
                                                    "PATI?O", "PATIÑO"
                                                ),
                                                "VILLASE?OR", "VILLASEÑOR"
                                            ),
                                            "MAR?A", "MARÍA"
                                        ),
                                        "CA?IZO", "CAÑIZO"
                                    ),
                                    "MU?OZ", "MUÑOZ"
                                ),
                                "JIM?NEZ", "JIMÉNEZ"
                            ),
                            "G?MEZ", "GÓMEZ"
                        ),
                        "HERV?S", "HERVÁS"
                    ),
                    "P?REZ", "PÉREZ"
                ),
                "MART?NEZ", "MARTÍNEZ"
            ) AS cliente,
            REPLACE(
                REPLACE(
                    REPLACE(
                       REPLACE(REPLACE(
                            TRIM(REGEXP_REPLACE(ct.name, "\\\\s{2,}", " ")),
                            "&amp;", "&"
                        ),"&#039;","\'"),
                        "LE?N", "LEÓN"
                    ),
                    "ADMINISTRACI?N", "ADMINISTRACIÓN"
                ),
                "CONSTRUCCI?N", "CONSTRUCCIÓN"
            ) AS empresa,
            ', departamento_columns, '
        FROM contract ct
        INNER JOIN customer c ON ct.customerId = c.customerId
        LEFT JOIN temp_costos_agrupados tca ON ct.contractId = tca.contractId
        WHERE ct.contractId IN (
                SELECT DISTINCT contractId FROM temp_servicio_costos
            )');
    
    -- Agregar filtros condicionales
    IF p_personal_id IS NOT NULL THEN
        SET sql_query = CONCAT(sql_query, '
            AND EXISTS (
                SELECT 1 FROM contractPermiso cp
                INNER JOIN personal p ON cp.personaId = p.personalId
                WHERE cp.contractId = ct.contractId
                AND (
                    p.personalId = ', p_personal_id, '
                    OR ("', subordinados_list, '" != "" AND FIND_IN_SET(p.personalId, "', subordinados_list, '") > 0)
                )
            )');
    END IF;
    
    IF p_departamento_id IS NOT NULL THEN
        SET sql_query = CONCAT(sql_query, '
            AND ct.contractId IN (
                SELECT contractId FROM temp_servicio_costos 
                WHERE departamentoId = ', p_departamento_id, '
            )');
    END IF;
    
    IF p_cliente_nombre IS NOT NULL THEN
        SET sql_query = CONCAT(sql_query, '
            AND c.nameContact LIKE "%', p_cliente_nombre, '%"');
    END IF;
    
    IF p_empresa_nombre IS NOT NULL THEN
        SET sql_query = CONCAT(sql_query, '
            AND ct.name LIKE "%', p_empresa_nombre, '%"');
    END IF;
    
    -- Agregar GROUP BY y ORDER BY
    SET sql_query = CONCAT(sql_query, '
        GROUP BY ct.contractId, c.customerId, c.nameContact, ct.name
        HAVING COALESCE(SUM(tca.total_departamento), 0) > 0
        ORDER BY c.nameContact ASC, ct.name ASC');
    
    -- Ejecutar la consulta dinámica
    SET @final_query = sql_query;
    PREPARE stmt FROM @final_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    -- Restaurar el límite original de GROUP_CONCAT
    SET SESSION group_concat_max_len = 1024;
    
    -- Limpiar tablas temporales
    DROP TEMPORARY TABLE temp_departamentos;
    DROP TEMPORARY TABLE temp_servicio_costos;
    DROP TEMPORARY TABLE temp_costos_agrupados;
    
END$$

DELIMITER ;

-- Ejemplo de uso:
-- CALL sp_reporte_montos_por_departamento(2025, NULL, NULL, NULL, NULL, NULL); -- Todos los registros del 2025 por departamento
-- CALL sp_reporte_montos_por_departamento(2025, 6, NULL, NULL, NULL, NULL); -- Acumulado de enero a junio del 2025
-- CALL sp_reporte_montos_por_departamento(2025, NULL, 123, NULL, NULL, NULL); -- Filtrado por personal y sus subordinados
-- CALL sp_reporte_montos_por_departamento(2025, 3, NULL, 5, 'Juan', 'Empresa ABC'); -- Acumulado enero-marzo con múltiples filtros