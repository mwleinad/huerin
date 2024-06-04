ALTER TABLE `personal`
    ADD COLUMN `nivel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT "Nivel 1" AFTER `cuentaInhouse`;