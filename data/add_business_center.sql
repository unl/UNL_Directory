ALTER TABLE `departments`
    ADD `bc_org_unit` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL AFTER `org_unit`,
    ADD `bc_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL AFTER `bc_org_unit`,
ADD INDEX ( `bc_org_unit` ),
ADD INDEX ( `bc_name`(255) );