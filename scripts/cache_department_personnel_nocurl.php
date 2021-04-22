<?php
require_once 'www/config.inc.php';

// NOTE: This script simulates UNL_Peoplefinder_Department_PersonnelSubtree and
// any changes to it may need to be replicated here.

// Departments to cache personnel
$deptOrgUnits = array(
	50000955,
	50000907,
	50000897,
	50000905,
	50000928,
	50001078,
	50000834,
	50000908,
	50001081,
	50007300,
	50001088,
	50000899,
	50000828,
	50000829
);
$departments = new UNL_Officefinder_DepartmentList_OfficialOrgUnits();
var_dump($departments);
die();

$driver = new UNL_Peoplefinder_Driver_LDAP();
$peopleFinder = new UNL_Peoplefinder(array('driver' => $driver, 'reset-cache' => TRUE));
$peopleFinder::$resultLimit = 800;
echo "\n\nProcessing org units  with reset cache.\n";
foreach ($deptOrgUnits as $orgUnit) {
	$start = time();
	echo $orgUnit . " started at " . date("h:i:s a", $start) . "\n";
	$department = UNL_Officefinder_Department::getByorg_unit($orgUnit);
	$orgUnits = array();
	$orgUnits[] = $department->org_unit;
	getChildOrgUnits($department, $orgUnits);
	$results = $peopleFinder->getHROrgUnitNumbersMatches($orgUnits);
	$end = time();
	$duration = ($end - $start) / 60;
	echo $orgUnit . " finished at " . date("h:i:s a", $end) . " and took " . round($duration, 3) . " minutes.\n\n";
}

function getChildOrgUnits($department, &$orgUnits)
{
	foreach ($department->getOfficialChildDepartments() as $sub_dept) {
		$orgUnits[] = $sub_dept->org_unit;
		getChildOrgUnits($sub_dept, $orgUnits);
	}
}
