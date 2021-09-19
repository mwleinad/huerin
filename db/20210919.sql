CREATE TABLE `work_team` (
 `id` INT(11) NOT NULL AUTO_INCREMENT,
 `name` VARCHAR(255) NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

CREATE TABLE `personal_work_team` (
`personal_id` INT(11) NULL DEFAULT NULL,
`work_team_id` INT(11) NULL DEFAULT NULL,
`departament_id` INT(11) NULL DEFAULT NULL
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
