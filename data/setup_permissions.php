<?php
require_once dirname(__FILE__).'/../www/config.inc.php';
error_reporting(E_ALL | E_STRICT);

$permissions = new SplFileObject(dirname(__FILE__).'/unl org units with contacts for help desk-updated 8-2010.csv');
$permissions->setFlags(SplFileObject::READ_CSV);

foreach ($permissions as $row) {

    list($org_unit, $dept_name, $hr_name, $phone, $personnel_num, $uid) = $row;

    $department = UNL_Officefinder_Department::getByOrg_unit($org_unit);
    if ($department) {
        $department->addUser($uid);
    }
}