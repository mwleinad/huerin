SET FOREIGN_KEY_CHECKS = 0;
-- ----------------------------
-- Table structure for tipo_clasificacion_cliente
-- ----------------------------
DROP TABLE IF EXISTS `tipo_clasificacion_cliente`;
CREATE TABLE `tipo_clasificacion_cliente`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
    `fecha_registro` datetime NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_eliminado` datetime NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci;

-- ----------------------------
-- Records of tipo_clasificacion_cliente
-- ----------------------------
BEGIN;
INSERT INTO `tipo_clasificacion_cliente` (`id`, `nombre`, `fecha_registro`, `fecha_eliminado`) VALUES (1, 'A', '2024-02-11 09:04:22', NULL), (2, 'B', '2024-02-11 09:04:25', NULL), (3, 'C', '2024-02-11 09:04:29', NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE `personal`ADD COLUMN `mailGrupo` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `numberAccountsAllowed`,
ADD COLUMN `listaDistribucion` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `mailGrupo`,
ADD COLUMN `cuentaInhouse` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `listaDistribucion`;

ALTER TABLE `customer`
    ADD COLUMN `tipo_clasificacion_cliente_id` bigint UNSIGNED NULL DEFAULT NULL AFTER `name_referrer`;
INSERT INTO `nameFields` (`clave`, `name`) VALUES ('tipo_clasificacion_cliente_id', 'Clasificacion');

ALTER TABLE `customer`
    MODIFY COLUMN `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL AFTER `customerId`,
    MODIFY COLUMN `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL AFTER `name`,
    MODIFY COLUMN `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL AFTER `phone`,
    MODIFY COLUMN `encargadoCuenta` int NULL DEFAULT NULL AFTER `nameContact`,
    MODIFY COLUMN `responsableCuenta` int NULL DEFAULT NULL AFTER `encargadoCuenta`;

UPDATE `tipo_clasificacion` SET `nombre` = 'A' WHERE `id` = 1;
UPDATE `tipo_clasificacion` SET `nombre` = 'B' WHERE `id` = 2;
UPDATE `tipo_clasificacion` SET `nombre` = 'C' WHERE `id` = 3;


