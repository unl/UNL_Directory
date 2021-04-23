<?php
require_once 'www/config.inc.php';

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

$driver = new UNL_Peoplefinder_Driver_LDAP();
echo "\n\nProcessing UNL_Peoplefinder_Department_PersonnelSubtree org units with reset-cache.\n";
foreach ($deptOrgUnits as $orgUnit) {
	$start = time();
	echo $orgUnit . " started at " . date("h:i:s a", $start) . "\n";
	$peopleFinder = new UNL_Peoplefinder_Department_PersonnelSubtree(array('driver' => $driver, 'reset-cache' => TRUE, 'org_unit' => $orgUnit));
	$end = time();
	$duration = ($end - $start) / 60;
	echo $orgUnit . " finished at " . date("h:i:s a", $end) . " and took " . round($duration, 3) . " minutes.\n\n";
}
