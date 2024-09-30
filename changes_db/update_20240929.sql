ALTER TABLE `personal`
ADD COLUMN `numeroCelularInstitucional` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `skype`,
ADD COLUMN `numeroTelefonicoWebex` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `numeroCelularInstitucional`,
ADD COLUMN `extensionWebex` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `numeroTelefonicoWebex`;

INSERT INTO `permisos` (`titulo`, `parentId`, `levelDeep`) VALUES ('Número celular institucional', 224, 3);
INSERT INTO `permisos` (`titulo`, `parentId`, `levelDeep`) VALUES ('Número telefónico de Webex', 224, 3);
INSERT INTO `permisos` (`titulo`, `parentId`, `levelDeep`) VALUES ('Extensión Webex', 224, 3);