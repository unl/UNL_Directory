<?php
/**
 * This script sets the suppress flag for official org units from SAP with no
 * appointments and children.
 */
require_once dirname(__FILE__).'/../www/config.inc.php';

// seed the peoplefinder instance with configured driver
UNL_Peoplefinder::getInstance(isset($driver) ? ['driver' => $driver] : []);

// Step 1.) Reset suppress flag on all departments to false (0)
$db = UNL_Officefinder::getDB();
$db->query('UPDATE departments SET suppress = 0;');


// Step 2.) Suppress all departments with (no children AND no hr personnel)
$departments = new UNL_Officefinder_DepartmentList_OfficialOrgUnitsNoChildren();
$departments_with_nohrpersonnel = new UNL_Officefinder_DepartmentList_Filter_HasNoHRPersonnel($departments);

foreach ($departments_with_nohrpersonnel as $department) {
    /* @var $department UNL_Officefinder_Department */
    echo 'Hiding ' . $department->name . ' (' . $department->org_unit . ')' . PHP_EOL;
    $department->suppress = true;
    $department->save();
}


// Step 3.) Suppress parent if needed
function checkChildren(UNL_Officefinder_Department $parent)
{
    foreach ($parent->getChildren() as $child) {
        if (!$child->suppress) {
            // This listing has a child which is visible
            return;
        }
    }
    echo 'Hiding parent ' . $parent->name . ' (' . $parent->org_unit . ')' . PHP_EOL;
    $parent->suppress = 1;
    $parent->save();

    if (!$parent->isRoot()) {
        return checkChildren($parent->getParent());
    }
}

$suppressed_depts = new UNL_Officefinder_DepartmentList_Suppressed();
foreach ($suppressed_depts as $suppressed) {
    /* @var $suppressed UNL_Officefinder_Department */
    $parent = $suppressed->getParent();
    checkChildren($parent);
}
