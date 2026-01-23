ALTER TABLE `payment`
ADD COLUMN `tipoDeMoneda` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'MXN' AFTER `paymentDate`,
ADD COLUMN `tipoCambio` decimal(10,4) NULL DEFAULT 1.0000 AFTER `tipoDeMoneda`,
ADD COLUMN `originalAmount` decimal(14,4) NULL DEFAULT NULL AFTER `tipoCambio`;

ALTER TABLE `comprobante` 
MODIFY COLUMN `tipoDeCambio` decimal(20, 6) UNSIGNED NOT NULL DEFAULT 1.000000 AFTER `tipoDeMoneda`;