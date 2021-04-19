<?php
require_once __DIR__.'/../www/config.inc.php';

// Departments to cache personnel
$deptOrgUnits = array(
	50000907,
	50000897,
	50000955,
	50001078,
	50000834,
	50000908,
	50001081,
	50007300,
	50001088,
	50000899,
	50000928,
	50000828,
	50000829,
	50000905
);

foreach ($deptOrgUnits as $orgUnit) {
	$url = 'https://directory-test.unl.edu/departments/' . $orgUnit . '/personnelsubtree?format=xml&reset-cache';
	$ch = curl_init();
	$timeout = 400;

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

	$lines_string = curl_exec($ch);
	curl_close($ch);
}
