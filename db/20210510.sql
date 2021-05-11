ALTER TABLE `office_resource`
    ADD COLUMN `vencimiento` DATE NULL DEFAULT NULL AFTER `codigo_activacion`;
