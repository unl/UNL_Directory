ALTER TABLE `departments` ADD `academic` BOOLEAN NULL DEFAULT NULL COMMENT 'Flag to indicate if department is academic' AFTER `website` ,
ADD INDEX ( `academic` ) 