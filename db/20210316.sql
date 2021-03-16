ALTER TABLE `tipoServicio`
    ADD COLUMN `uniqueInvoice` TINYINT NOT NULL DEFAULT 0 AFTER `mostrarCostoVisual`;
