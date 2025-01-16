CREATE TABLE IF NOT EXISTS `#__visitacao_grupo` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT 1,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nome` VARCHAR(255)  NOT NULL ,
`telefone` VARCHAR(255)  NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`idadecriancas` VARCHAR(255)  NOT NULL ,
`numerocriancas` VARCHAR(255)  NOT NULL ,
`adultos` VARCHAR(255)  NOT NULL ,
`opcoes` VARCHAR(255)  NOT NULL ,
`date` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
`visita` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Cadastro','com_visitacao_grupo.cadastro','{"special":{"dbtable":"#__visitacao_grupo","key":"id","type":"Cadastro","prefix":"Visitacao_grupTable"}}', '{"formFile":"administrator\/components\/com_visitacao_grupo\/models\/forms\/cadastro.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_visitacao_grupo.cadastro')
) LIMIT 1;
