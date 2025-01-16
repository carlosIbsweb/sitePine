CREATE TABLE IF NOT EXISTS `#__aniversarios_eventos` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nomeresp` VARCHAR(255)  NOT NULL ,
`telefone` VARCHAR(255)  NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`nomecria` VARCHAR(255)  NOT NULL ,
`idade` VARCHAR(255)  NOT NULL ,
`escola` VARCHAR(255)  NOT NULL ,
`diafesta` VARCHAR(255)  NOT NULL ,
`numerocria` VARCHAR(255)  NOT NULL ,
`numeroadult` VARCHAR(255)  NOT NULL ,
`horainicio` VARCHAR(255)  NOT NULL ,
`tema` VARCHAR(255)  NOT NULL ,
`checkbox` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

