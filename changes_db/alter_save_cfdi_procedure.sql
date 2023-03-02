DELIMITER //
DROP PROCEDURE IF EXISTS `sp_saveInvoice`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_saveInvoice`(IN pUserId int,IN pFormaDePago varchar(255),IN pCondicionPago varchar(255),IN pMetodoPago varchar(255),IN pTasaIva varchar(20),IN pTipoMoneda varchar(255),IN pTipoCambio varchar(255),IN pPorcentajeRetIva VARCHAR(20),IN pPorcentajeRetIsr VARCHAR(20),IN pTipoCompId int,IN pPorcentajeIeps double,IN pPorcentajeDescuento double,IN pEmpresaId int,IN pSucursalId int,IN pObservaciones text,IN pSerie varchar(255),IN pFolio varchar(255),IN pFecha TIMESTAMP,IN pSello longtext,IN pNoAprobacion varchar(255),IN pAnoAprobacion int,IN pNoCertificado varchar(255),IN pCertificado varchar(255),IN pSubtotal float(20,6),IN pDescuento float(20,6),IN pMotivoDescuento varchar(255),IN pTotal float(20,6),IN pTipoComprobante varchar(255),IN pXml varchar(255),IN pRfcId int,IN pIvaTotal float(20,6),IN pDataSerialize longtext,IN pConceptosSerialize longtext,IN pImpuestosSerialize longtext,IN pCadenaOriginalSerialize longtext,IN pTimbreFiscal longtext,IN pProcedencia varchar(255),IN pServicioId int,IN pParentId int,IN pVersion varchar(20), IN pSerieId INT)
BEGIN
	#Routine body goes here...
	DECLARE lastId INT;

INSERT INTO comprobante (
    comprobanteId,
    userId,
    formaDePago,
    condicionesDePago,
    metodoDePago,
    tasaIva,
    tipoDeMoneda,
    tipoDeCambio,
    porcentajeRetIva,
    porcentajeRetIsr,
    tiposComprobanteId,
    porcentajeIEPS,
    porcentajeDescuento,
    empresaId,
    sucursalId,
    observaciones,
    serie,
    folio,
    fecha,
    sello,
    noAprobacion,
    anoAprobacion,
    noCertificado,
    certificado,
    subtotal,
    descuento,
    motivoDescuento,
    total,
    tipoDeComprobante,
    xml,
    rfcId,
    ivaTotal,
    data,
    conceptos,
    impuestos,
    cadenaOriginal,
    timbreFiscal,
    procedencia,
    servicioId,
    parentId,
    version
)
VALUES
    (
        NULL,
        pUserId,
        pFormaDePago,
        pCondicionPago,
        pMetodoPago,
        pTasaIva,
        pTipoMoneda,
        pTipoCambio,
        pPorcentajeRetIva,
        pPorcentajeRetIsr,
        pTipoCompId,
        pPorcentajeIeps,
        pPorcentajeDescuento,
        pEmpresaId,
        pSucursalId,
        pObservaciones,
        pSerie,
        pFolio,
        pFecha,
        pSello,
        pNoAprobacion,
        pAnoAprobacion,
        pNoCertificado,
        pCertificado,
        pSubtotal,
        pDescuento,
        pMotivoDescuento,
        pTotal,
        pTipoComprobante,
        pXml,
        pRfcId,
        pIvaTotal,
        pDataSerialize,
        pConceptosSerialize,
        pImpuestosSerialize,
        pCadenaOriginalSerialize,
        pTimbreFiscal,
        pProcedencia,
        pServicioId,
        pParentId,
        pVersion
    );

SET lastId =  LAST_INSERT_ID();
			IF (lastId)  THEN
UPDATE serie SET consecutivo = consecutivo + 1 WHERE serieId = pSerieId;
END IF;

SELECT lastId;
END //
DELIMITER ;
