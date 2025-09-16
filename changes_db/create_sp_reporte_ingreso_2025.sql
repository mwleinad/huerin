DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_reporte_ingreso_anual`$$

CREATE PROCEDURE `sp_reporte_ingreso_anual`(
    IN p_anio INT,
    IN p_personal_id INT,
    IN p_departamento_id INT,
    IN p_cliente_nombre VARCHAR(255),
    IN p_empresa_nombre VARCHAR(255),
    IN p_mes_limite INT
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE subordinados_list TEXT DEFAULT '';
    
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
    
    -- Query principal del reporte
    SELECT 
        trim(c.nameContact) AS cliente,
        trim(ct.name) AS empresa,
        COALESCE(trim(p.name), 'Sin Encargado') AS encargado,
        trim(ts.nombreServicio) AS servicio,
        s.status AS estatus,
        trim(d.departamento) AS departamento,
        trim(ts.periodicidad) AS periodicidad,
        
        -- Enero
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 1 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 1 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 1), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 1), 0)
            ELSE 0
        END AS enero,
        
        -- Febrero
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 2 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 2 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 2), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 2), 0)
            ELSE 0
        END AS febrero,
        
        -- Marzo
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 3 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 3 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 3), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 3), 0)
            ELSE 0
        END AS marzo,
        
        -- Abril
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 4 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 4 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 4), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 4), 0)
            ELSE 0
        END AS abril,
        
        -- Mayo
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 5 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 5 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 5), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 5), 0)
            ELSE 0
        END AS mayo,
        
        -- Junio
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 6 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 6 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 6), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 6), 0)
            ELSE 0
        END AS junio,
        
        -- Julio
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 7 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 7 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 7), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 7), 0)
            ELSE 0
        END AS julio,
        
        -- Agosto
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 8 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 8 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 8), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 8), 0)
            ELSE 0
        END AS agosto,
        
        -- Septiembre
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 9 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 9 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 9), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 9), 0)
            ELSE 0
        END AS septiembre,
        
        -- Octubre
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 10 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 10 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 10), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 10), 0)
            ELSE 0
        END AS octubre,
        
        -- Noviembre
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 11 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 11 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 11), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 11), 0)
            ELSE 0
        END AS noviembre,
        -- Diciembre
        CASE 
            WHEN p_mes_limite IS NOT NULL AND 12 > p_mes_limite THEN 0
            WHEN s.status = 'bajaParcial' AND YEAR(s.lastDateWorkflow) = p_anio AND MONTH(s.lastDateWorkflow) >= 12 THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 12), 0)
            WHEN s.status != 'bajaParcial' THEN
                COALESCE((SELECT SUM(ins.costoWorkflow) 
                         FROM instanciaServicio ins 
                         WHERE ins.servicioId = s.servicioId 
                         AND YEAR(ins.date) = p_anio AND MONTH(ins.date) = 12), 0)
            ELSE 0
        END AS diciembre
        
    FROM servicio s
    INNER JOIN contract ct ON s.contractId = ct.contractId
    INNER JOIN customer c ON ct.customerId = c.customerId
    INNER JOIN tipoServicio ts ON s.tipoServicioId = ts.tipoServicioId
    LEFT JOIN departamentos d ON ts.departamentoId = d.departamentoId
    LEFT JOIN contractPermiso cp ON ct.contractId = cp.contractId 
        AND cp.departamentoId = d.departamentoId
    LEFT JOIN personal p ON cp.personalId = p.personalId
    
    WHERE 
        -- Solo servicios activos o en bajaParcial
        s.status IN ('activo', 'bajaParcial')
        
        -- Solo tipos de servicio primarios
        AND ts.is_primary = 1
        
        -- Filtro de año (obligatorio)
        AND EXISTS (
            SELECT 1 FROM instanciaServicio ins 
            WHERE ins.servicioId = s.servicioId 
            AND YEAR(ins.date) = p_anio
        )
        
        -- Filtro de personal (si se especifica)
        AND (
            p_personal_id IS NULL 
            OR p.personalId = p_personal_id
            OR (subordinados_list != '' AND FIND_IN_SET(p.personalId, subordinados_list) > 0)
        )
        
        -- Filtro de departamento (si se especifica)
        AND (p_departamento_id IS NULL OR (d.departamentoId IS NOT NULL AND d.departamentoId = p_departamento_id))
        
        -- Filtro de nombre cliente (si se especifica)
        AND (
            p_cliente_nombre IS NULL 
            OR c.nameContact LIKE CONCAT('%', p_cliente_nombre, '%')
        )
        
        -- Filtro de nombre empresa (si se especifica)
        AND (
            p_empresa_nombre IS NULL 
            OR ct.name LIKE CONCAT('%', p_empresa_nombre, '%')
        )
    
    HAVING (
        -- Solo filas donde al menos un monto mensual es mayor a 0
        GREATEST(
            COALESCE(Enero, 0), COALESCE(Febrero, 0), COALESCE(Marzo, 0), COALESCE(Abril, 0),
            COALESCE(Mayo, 0), COALESCE(Junio, 0), COALESCE(Julio, 0), COALESCE(Agosto, 0),
            COALESCE(Septiembre, 0), COALESCE(Octubre, 0), COALESCE(Noviembre, 0), COALESCE(Diciembre, 0)
        ) > 0
    )
    
    ORDER BY 
        c.nameContact,
        ct.name,
        ts.nombreServicio;
    
END$$

DELIMITER ;

-- Ejemplo de uso:
-- CALL sp_reporte_ingreso_anual(2025, NULL, NULL, NULL, NULL); -- Todos los registros del 2025
-- CALL sp_reporte_ingreso_anual(2025, 123, NULL, NULL, NULL); -- Filtrado por personal ID 123 y sus subordinados
-- CALL sp_reporte_ingreso_anual(2025, NULL, 5, 'Juan', 'Empresa ABC'); -- Con múltiples filtros
