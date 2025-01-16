CREATE TABLE IF NOT EXISTS `#__aniversarios_e_eventos` (
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
`nome_cria` VARCHAR(255)  NOT NULL ,
`idade` VARCHAR(255)  NOT NULL ,
`escola` VARCHAR(255)  NOT NULL ,
`festa` VARCHAR(255)  NOT NULL ,
`numerocriancas` VARCHAR(255)  NOT NULL ,
`adultos` VARCHAR(255)  NOT NULL ,
`tema` VARCHAR(255)  NOT NULL ,
`opcoes` VARCHAR(255)  NOT NULL ,
`opcional` VARCHAR(255)  NOT NULL ,
`date` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

