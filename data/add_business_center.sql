ALTER TABLE `departments`
    ADD `business_center_org_unit` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL AFTER `org_unit`,
    ADD `business_center_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL AFTER `business_center_org_unit`,
ADD INDEX ( `business_center_org_unit` ),
ADD INDEX ( `business_center_name`(255) );