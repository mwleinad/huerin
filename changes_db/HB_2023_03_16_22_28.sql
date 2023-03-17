ALTER TABLE `contract`
    ADD COLUMN `acuerdo_comercial` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `qualification`;

INSERT INTO `permisos`(`titulo`, `parentId`, `levelDeep`) VALUES ('Agregar o actualizar acuerdo comercial', 62, 3)
