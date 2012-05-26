<?php
require_once dirname(__FILE__).'/../www/config.inc.php';
error_reporting(E_ALL | E_STRICT);

ini_set("auto_detect_line_endings", true);

$permissions = new SplFileObject(dirname(__FILE__).'/UNL Org Units with Contacts for Help Desk-Updated 4-2012.csv');
$permissions->setFlags(SplFileObject::READ_CSV);

foreach ($permissions as $row) {

    list($org_unit, $dept_name, $hr_name, $phone, $personnel_num, $uid) = $row;
    $department = UNL_Officefinder_Department::getByOrg_unit($org_unit);
    if ($department) {
        $department->addUser($uid);
    }
}