<?php
/**
 * This script sets the suppress flag for official org units from SAP with no
 * appointments and children.
 */
require_once dirname(__FILE__).'/../www/config.inc.php';

$departments = new UNL_Officefinder_DepartmentList_OfficialOrgUnits();

$departments_without_children = new UNL_Officefinder_DepartmentList_Filter_HasNoChildren($departments);

$departments_with_nohrpersonnel = new UNL_Officefinder_DepartmentList_Filter_HasNoHRPersonnel($departments_without_children);

foreach ($departments_with_nohrpersonnel as $department) {
    /* @var $department UNL_Officefinder_Department */
    echo 'Hiding ' . $department->name . ' (' . $department->org_unit . ')' . PHP_EOL;
    $department->suppress = true;
    $department->save();
}