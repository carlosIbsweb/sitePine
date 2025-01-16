CREATE TABLE IF NOT EXISTS `#__pine_vacation_fun` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT 1,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nome_resp` VARCHAR(255)  NOT NULL ,
`telefone` VARCHAR(255)  NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`visita` VARCHAR(255)  NOT NULL ,
`numerocriancas` VARCHAR(255)  NOT NULL ,
`cardapio` TEXT NOT NULL ,
`nome_crianca1` VARCHAR(255)  NOT NULL ,
`idade_crianca1` VARCHAR(255)  NOT NULL ,
`nome_crianca2` VARCHAR(255)  NOT NULL ,
`idade_crianca2` VARCHAR(255)  NOT NULL ,
`nome_crianca3` VARCHAR(255)  NOT NULL ,
`idade_crianca3` VARCHAR(255)  NOT NULL ,
`nome_crianca4` VARCHAR(255)  NOT NULL ,
`idade_crianca4` VARCHAR(255)  NOT NULL ,
`nome_crianca_add1` VARCHAR(255)  NOT NULL ,
`idade_crianca_add1` VARCHAR(255)  NOT NULL ,
`nome_crianca_add2` VARCHAR(255)  NOT NULL ,
`idade_crianca_add2` VARCHAR(255)  NOT NULL ,
`date` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
`cpf` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Cadastro','com_pine_vacation_fun.cadastro','{"special":{"dbtable":"#__pine_vacation_fun","key":"id","type":"Cadastro","prefix":"Pine_vacation_funTable"}}', '{"formFile":"administrator\/components\/com_pine_vacation_fun\/models\/forms\/cadastro.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"cardapio"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_pine_vacation_fun.cadastro')
) LIMIT 1;
