CREATE TABLE IF NOT EXISTS `#__picnic_` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nome` VARCHAR(255)  NOT NULL ,
`telefone` VARCHAR(255)  NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`numeroadult` VARCHAR(255)  NOT NULL ,
`numerocria` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

