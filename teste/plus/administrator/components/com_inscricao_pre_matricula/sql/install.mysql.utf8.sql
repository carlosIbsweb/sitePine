CREATE TABLE IF NOT EXISTS `#__inscricao_pre_matricula` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL DEFAULT 0,
`state` TINYINT(1)  NOT NULL DEFAULT 1,
`checked_out` INT(11)  NOT NULL DEFAULT 0,
`checked_out_time` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
`created_by` INT(11)  NOT NULL DEFAULT 0,
`modified_by` INT(11)  NOT NULL DEFAULT 0,
`nome` VARCHAR(255)  NOT NULL ,
`telefone` VARCHAR(255)  NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Inscrição','com_inscricao_pre_matricula.inscricao','{"special":{"dbtable":"#__inscricao_pre_matricula","key":"id","type":"Inscricao","prefix":"Inscricao_pre_matriculaTable"}}', CASE 
                                WHEN 'field_mappings' is null THEN ''
                                ELSE ''
                                END as field_mappings, '{"formFile":"administrator\/components\/com_inscricao_pre_matricula\/models\/forms\/inscricao.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_inscricao_pre_matricula.inscricao')
) LIMIT 1;
