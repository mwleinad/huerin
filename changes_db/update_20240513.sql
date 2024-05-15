DELIMITER //
DROP PROCEDURE IF EXISTS `huerin`.`sp_get_data_reporte_cobranza_detallado`;

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_data_reporte_cobranza_detallado`(IN `anio` INT, IN `mes` INT)
BEGIN

  DECLARE sin_conceptos BOOLEAN DEFAULT FALSE;
	DECLARE vControl INT DEFAULT 0;
	DECLARE vTotalRow  INT DEFAULT 0;
	DECLARE vTotalRowConcepto  INT DEFAULT 0;
	DECLARE vCurrentRow INT DEFAULT 1;
	DECLARE vCurrentRowConcepto INT DEFAULT 1;

	DECLARE vComprobanteId BIGINT;
	DECLARE vTotalFactura DECIMAL(10,2);
	DECLARE vPagado VARCHAR(2);
	DECLARE vServicioId BIGINT;
	DECLARE vFecha DATE;
	DECLARE vSerie VARCHAR(100);
	DECLARE vFolio BIGINT;
	DECLARE vCliente VARCHAR(255);
	DECLARE vRazon VARCHAR(255);
	DECLARE vNombreServicio VARCHAR(255);
	DECLARE vStatusServicio VARCHAR(255);
	DECLARE vArea VARCHAR(255);
	DECLARE vCostoServicio DECIMAL(10,2);
	DECLARE vPagosAfactura DECIMAL(10,2);
	DECLARE vResponsables LONGTEXT;


	DECLARE cursor_comprobantes CURSOR FOR
SELECT comprobanteId,comprobante.fecha,comprobante.serie,comprobante.folio,comprobante.total,empresas.cliente,empresas.razon_social,responsables
FROM comprobante INNER JOIN (
    SELECT contract.contractId id,customer.nameContact cliente, contract.name razon_social,
           (SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('departamento_id', contractPermiso.departamentoId, 'departamento',
                                                       departamentos.departamento, 'personal_id', contractPermiso.personalId, 'nombre', personal.name)), ']')
            FROM contractPermiso
                     INNER JOIN personal ON contractPermiso.personalId = personal.personalId
                     INNER JOIN departamentos ON contractPermiso.departamentoId = departamentos.departamentoId
            WHERE contractPermiso.contractId = contract.contractId
            GROUP BY contractPermiso.contractId) as responsables
    FROM contract
             INNER JOIN customer ON contract.customerId =  customer.customerId
) empresas ON comprobante.userId = empresas.id
WHERE comprobante.status = '1'
    AND year(comprobante.fecha) = anio
  AND comprobante.tiposComprobanteId = 1
  AND NOT EXISTS (SELECT solicitud_cancelacion_id FROM pending_cfdi_cancel WHERE cfdi_id = comprobante.comprobanteId);

SELECT count(*)
FROM comprobante INNER JOIN (
    SELECT contract.contractId id,customer.nameContact cliente, contract.name razon_social
    FROM contract
             INNER JOIN customer ON contract.customerId =  customer.customerId
) empresas ON comprobante.userId = empresas.id
WHERE comprobante.status = '1'
    AND year(comprobante.fecha) = anio
  AND comprobante.tiposComprobanteId = 1
  AND NOT EXISTS (SELECT solicitud_cancelacion_id FROM pending_cfdi_cancel WHERE cfdi_id = comprobante.comprobanteId) INTO vTotalRow;

DROP TEMPORARY TABLE IF EXISTS tmp_data_cobranza;
	CREATE TEMPORARY TABLE IF NOT EXISTS tmp_data_cobranza(fecha DATE, serie VARCHAR(100), `folio` BIGINT, `total_factura` DECIMAL(10,2), cliente VARCHAR(255),razon_social varchar(255), `servicio` VARCHAR(255),`estatus_servicio` VARCHAR(255),`area` VARCHAR(255), `costo_servicio` DECIMAL(10,2),`pagado` VARCHAR(2),`responsables` JSON);

OPEN cursor_comprobantes;
itera_comprobante: REPEAT
			FETCH cursor_comprobantes INTO vComprobanteId,vFecha,vSerie,vFolio,vTotalFactura,vCliente,vRazon,vResponsables;


			set vRazon = REPLACE( TRIM( vRazon ), '&amp;', '&' );

			IF !JSON_VALID(vResponsables)  THEN
			 SET vResponsables =  null;

END IF;

			BLOCK2: BEGIN
        DECLARE cursor_conceptos CURSOR FOR SELECT servicioId,valorUnitario FROM concepto WHERE comprobanteId = vComprobanteId;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET sin_conceptos = true;

OPEN cursor_conceptos;
itera_conceptos: loop
							FETCH cursor_conceptos INTO vServicioId,vCostoServicio;
							IF sin_conceptos THEN
								CLOSE cursor_conceptos;
							  SET sin_conceptos = false;
								LEAVE itera_conceptos;
END IF;

SELECT servicio.status,tipoServicio.nombreServicio,(SELECT departamento FROM departamentos WHERE departamentoId = tipoServicio.departamentoId LIMIT 1) area FROM servicio INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
WHERE servicio.servicioId = vServicioId  INTO vStatusServicio,vNombreServicio,vArea;


SELECT SUM(amount) FROM payment WHERE comprobanteId = vComprobanteId AND paymentStatus = 'activo' INTO vPagosAfactura;

CASE vStatusServicio
								WHEN vStatusServicio = 'activo' THEN
								 SET vStatusServicio =  'Activo';
WHEN vStatusServicio = 'baja' THEN
								 SET vStatusServicio =  'Baja';
WHEN vStatusServicio = 'bajaParcial' THEN
							   SET vStatusServicio =  'Baja Temporal';
END CASE;

							IF(vTotalFactura = vPagosAFactura) THEN
								SET vPagado = 'Si';
ELSE
							  SET vPagado = 'No';
END IF;

INSERT INTO tmp_data_cobranza(fecha,serie,folio,total_factura,cliente,razon_social,servicio,estatus_servicio,area,costo_servicio,pagado,responsables)VALUES(vFecha,vSerie,vFolio,vTotalFactura,vCliente,vRazon,vNombreServicio,vStatusServicio,vArea,vCostoServicio,vPagado,vResponsables);
END loop itera_conceptos;
END BLOCK2;

			SET vCurrentRow = vCurrentRow+1;
			UNTIL (vCurrentRow > vTotalRow)
END REPEAT itera_comprobante;
CLOSE cursor_comprobantes;

SELECT * FROM tmp_data_cobranza order by fecha ASC;

END;//

DELIMITER ;