RENAME TABLE `prospect` TO `prospect_back`;

CREATE TABLE `prospect` (
                            `id` INT(11) NOT NULL AUTO_INCREMENT,
                            `name` VARCHAR(255) NULL DEFAULT NULL,
                            `phone` VARCHAR(50) NULL DEFAULT NULL,
                            `email` VARCHAR(50) NULL DEFAULT NULL,
                            `observation` TEXT NULL DEFAULT NULL,
                            PRIMARY KEY (`id`)
)COLLATE='latin1_swedish_ci';

ALTER TABLE `prospect`
    ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() AFTER `observation`;

CREATE TABLE `company` (
                           `id` INT(11) NOT NULL AUTO_INCREMENT,
                           `prospect_id` INT(11) NOT NULL,
                           `name` VARCHAR(255) NULL DEFAULT NULL,
                           `is_new_company` TINYINT(4) NULL DEFAULT NULL,
                           `rfc` VARCHAR(13) NULL DEFAULT NULL,
                           `email` VARCHAR(50) NULL DEFAULT NULL,
                           `phone` VARCHAR(50) NULL DEFAULT NULL,
                           `legal_representative` VARCHAR(50) NULL DEFAULT NULL,
                           `observation` TEXT NULL DEFAULT NULL,
                           `constitution_date` DATE NULL DEFAULT NULL,
                           `created_at` TIMESTAMP NULL DEFAULT current_timestamp(),
                           `deleted_at` TIMESTAMP NULL DEFAULT NULL,
                           PRIMARY KEY (`id`)
)
    COLLATE='latin1_swedish_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=1;

CREATE TABLE `company_service` (
                                   `id` INT(11) NOT NULL AUTO_INCREMENT,
                                   `company_id` INT(11) NULL DEFAULT NULL,
                                   `service_id` INT(11) NULL DEFAULT NULL,
                                   PRIMARY KEY (`id`)
)
    COLLATE='latin1_swedish_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
;
