ALTER TABLE `customer`
    ADD COLUMN `is_referred` TINYINT NOT NULL DEFAULT 0 AFTER `observacion`,
    ADD COLUMN `type_referred` VARCHAR(255) NULL DEFAULT NULL AFTER `is_referred`,
    ADD COLUMN `partner_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `type_referred`,
    ADD COLUMN `name_referrer` TEXT NULL DEFAULT NULL AFTER `partner_id`;

INSERT INTO nameFields (`clave`, `name`) VALUES ('is_referred', 'Es cliente referido');
INSERT INTO nameFields (`clave`, `name`) VALUES ('type_referred', 'Referido por');
