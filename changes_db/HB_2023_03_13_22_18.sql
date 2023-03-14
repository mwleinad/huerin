ALTER TABLE `contract`
ADD COLUMN `alternativeType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `alternativeRzId`,
ADD COLUMN `alternativeRegimen` int(11) UNSIGNED NULL DEFAULT NULL AFTER `alternativeRfc`,
ADD COLUMN `alternativeUsoCfdi` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `alternativeRegimen`;
