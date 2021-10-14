ALTER TABLE `porcentajesBonos`
    CHANGE COLUMN `name` `name` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci' AFTER `porcentId`,
    ADD COLUMN `monto` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `porcentaje`;
