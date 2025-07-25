DELIMITER //
DROP PROCEDURE IF EXISTS `sp_get_data_reporte_cobranza_detallado`;

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_data_reporte_cobranza_detallado`(IN `anio` INT, IN `mes` INT)
BEGIN


  DECLARE sin_conceptos BOOLEAN DEFAULT FALSE;
	DECLARE vControl INT DEFAULT 0;
	DECLARE vTotalRow  INT DEFAULT 0;
	DECLARE vTotalRowConcepto  INT DEFAULT 0;
	DECLARE vCurrentRow INT DEFAULT 1;
	DECLARE vCurrentRowConcepto INT DEFAULT 1;

  DECLARE vFacturador VARCHAR(255);
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
	DECLARE vPagosPorServicio DECIMAL(10,2);
	DECLARE vFechaPago DATE;
	DECLARE vEstatusRs VARCHAR(15);
	DECLARE vDatosFacturacion LONGTEXT;
	DECLARE vResponsables LONGTEXT;

	DECLARE cursor_comprobantes CURSOR FOR
SELECT comprobanteId,comprobante.fecha,comprobante.serie,comprobante.folio,comprobante.total,empresas.cliente,empresas.razon_social,
       estatus_rs,datos_facturacion,(select razonSocial from rfc where rfc.rfcId=comprobante.rfcId limit 1) facturador,responsables
FROM comprobante INNER JOIN (
    SELECT contract.contractId id,customer.nameContact cliente, contract.name razon_social,
    contract.activo estatus_rs,
    CASE
    WHEN (contract.useAlternativeRzForInvoice = 1 AND contract.alternativeRzId = 0) THEN
    JSON_OBJECT('nombre',REPLACE( TRIM( contract.alternativeRz ), '&amp;', '&' ),'rfc',contract.alternativeRfc,'codigo_postal',contract.alternativeCp)
    WHEN (contract.useAlternativeRzForInvoice = 1 AND contract.alternativeRzId > 0) THEN
    (select JSON_OBJECT('nombre',REPLACE(TRIM( contract2.name ), '&amp;', '&' ),'rfc',contract2.rfc,'codigo_postal',contract2.cpAddress) from contract contract2 where contract2.contractId=contract.alternativeRzId limit 1)
    ELSE
    JSON_OBJECT('nombre',REPLACE( TRIM( contract.name ), '&amp;', '&' ),'rfc',contract.rfc,'codigo_postal',contract.cpAddress)
    END as datos_facturacion,
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
  AND NOT EXISTS (SELECT solicitud_cancelacion_id FROM pending_cfdi_cancel WHERE cfdi_id = comprobante.comprobanteId AND deleted_at IS NULL AND status = 'Pending');

SELECT count(*)
FROM comprobante INNER JOIN (
    SELECT contract.contractId id,customer.nameContact cliente, contract.name razon_social
    FROM contract
             INNER JOIN customer ON contract.customerId =  customer.customerId
) empresas ON comprobante.userId = empresas.id
WHERE comprobante.status = '1'
    AND year(comprobante.fecha) = anio
  AND comprobante.tiposComprobanteId = 1
  AND NOT EXISTS (SELECT solicitud_cancelacion_id FROM pending_cfdi_cancel WHERE cfdi_id = comprobante.comprobanteId AND deleted_at IS NULL AND status = 'Pending') INTO vTotalRow;

DROP TEMPORARY TABLE IF EXISTS tmp_data_cobranza;
	CREATE TEMPORARY TABLE IF NOT EXISTS tmp_data_cobranza(fecha DATE, serie VARCHAR(100), `folio` BIGINT, `total_factura` DECIMAL(10,2), cliente VARCHAR(255),razon_social varchar(255), `servicio` VARCHAR(255),`estatus_servicio` VARCHAR(255),`area` VARCHAR(255),`costo_servicio` DECIMAL(10,2),`pagado` VARCHAR(2),`pago_por_servicio` DECIMAL(10,2),`fecha_pago` DATE,`estatus_rs` VARCHAR(15),`datos_facturacion`JSON,`facturador` VARCHAR(255),`responsables` JSON);

OPEN cursor_comprobantes;
itera_comprobante: REPEAT
			FETCH cursor_comprobantes INTO vComprobanteId,vFecha,vSerie,vFolio,vTotalFactura,vCliente,vRazon,vEstatusRs,vDatosFacturacion,vFacturador,vResponsables;


			set vRazon = REPLACE( TRIM( vRazon ), '&amp;', '&' );

			IF !JSON_VALID(vResponsables)  THEN
			 SET vResponsables =  null;
END IF;

			IF vEstatusRs = "Si" THEN
					 SET vEstatusRs = "Activo";
ELSE
					 SET vEstatusRs = "Suspendido";
END IF;

			SET vPagosAfactura = 0;
SELECT SUM(amount) FROM payment WHERE comprobanteId = vComprobanteId AND paymentStatus = 'activo' group by comprobanteId INTO vPagosAfactura;

SET vFechaPago = null;

			#FECHA DE PAGO SIEMPRE EL ULTIMO
SELECT paymentDate FROM payment WHERE comprobanteId = vComprobanteId AND paymentStatus = 'activo' ORDER BY paymentDate DESC LIMIT 1 INTO vFechaPago;

set vPagado = null;
			IF(vTotalFactura = vPagosAfactura) THEN
				SET vPagado = 'Si';
			ELSEIF(vPagosAfactura > 0) THEN
				SET vPagado = 'P';
ELSE
			  SET vPagado = 'No';
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

							SET vPagosPorServicio = 0;
CASE
							 WHEN vPagado = 'Si' THEN
								SET vPagosPorServicio=vCostoServicio;
WHEN vPagado = 'P' THEN
								 SET vPagosPorServicio=vCostoServicio*(vPagosAfactura/vTotalFactura);
ELSE
							  SET vPagosPorServicio=0;
END CASE;


SELECT servicio.status,tipoServicio.nombreServicio,(SELECT departamento FROM departamentos WHERE departamentoId = tipoServicio.departamentoId LIMIT 1) area FROM servicio INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId WHERE servicio.servicioId = vServicioId  INTO vStatusServicio,vNombreServicio,vArea;

IF (vStatusServicio = 'activo') THEN
								 SET vStatusServicio =  'Activo';
						  ELSEIF(vStatusServicio = 'baja') THEN
								 SET vStatusServicio =  'Baja';
							ELSEIF (vStatusServicio = 'bajaParcial') THEN
							   SET vStatusServicio =  'Baja Temporal';
END IF;

INSERT INTO tmp_data_cobranza(fecha,serie,folio,total_factura,cliente,razon_social,servicio,estatus_servicio,area,costo_servicio,pagado,pago_por_servicio,fecha_pago,estatus_rs,datos_facturacion,facturador,responsables)VALUES(vFecha,vSerie,vFolio,vTotalFactura,vCliente,vRazon,vNombreServicio,vStatusServicio,vArea,vCostoServicio,vPagado,vPagosPorServicio,vFechaPago,vEstatusRs,vDatosFacturacion,vFacturador,vResponsables);
END loop itera_conceptos;
END BLOCK2;

			SET vCurrentRow = vCurrentRow+1;
			UNTIL (vCurrentRow > vTotalRow)
END REPEAT itera_comprobante;
CLOSE cursor_comprobantes;

SELECT * FROM tmp_data_cobranza order by fecha ASC;

END//
DELIMITER ;