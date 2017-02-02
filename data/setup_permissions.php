<?php
require_once __DIR__ . '/../www/config.inc.php';
error_reporting(E_ALL | E_STRICT);

ini_set("auto_detect_line_endings", true);

/*
 * Export the UNL HR contact list (go.unl.edu/hrcontacts) to CSV using the
 * same format as the hr_contacts_sample.csv 
 */
$permissions = new SplFileObject(__DIR__ . '/paf.csv');
$permissions->setFlags(SplFileObject::READ_CSV);

foreach ($permissions as $row) {

    list($org_unit, $dept_name, $hr_name, $phone, $personnel_num, $uid) = $row;
    $department = UNL_Officefinder_Department::getByOrg_unit($org_unit);
    if ($department) {
        $department->addUser($uid);
    }
}
