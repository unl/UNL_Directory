<?php
require_once __DIR__.'/../www/config.inc.php';

$baseURL = isset($argv) && !empty($argv[1]) ? $argv[1] : '';
if (empty($baseURL)) {
	die('Missing baseurl arg.  i.e. > php cache_department_personnel.php https://directory.unl.edu');
}

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

echo "\n\nProcessing org unit " . $baseURL . "/personnelsubstree pages with reset cache\n";
foreach ($deptOrgUnits as $orgUnit) {
	$start = time();
	echo $orgUnit . " started at " . date("h:i:s a", $start) . "\n";
	$url = $baseURL . '/departments/' . $orgUnit . '/personnelsubtree?format=xml&reset-cache';
	$ch = curl_init();
	$timeout = 400;

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

	$lines_string = curl_exec($ch);
	curl_close($ch);
	$end = time();
	$duration = ($end - $start) / 60;
	echo $orgUnit . " finished at " . date("h:i:s a", $end) . " took " . round($duration, 3) . " minutes\n\n";
}
