<?php

const MAX_RETRIES = 1;
const TIME_FORMAT = 'h:i:s a';
const MINUTES_LINE_END = " minutes.\n\n";

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
$deptOrgUnits = array(50000829);

echo "\n\nProcessing org unit " . $baseURL . "/personnelsubstree pages with reset cache.\n";
foreach ($deptOrgUnits as $orgUnit) {
	$success = FALSE;
	$attempts = 0;
	while ($success === FALSE && $attempts < MAX_RETRIES) {
		++$attempts;
		$start = time();
		echo "Attempt " . $attempts . " for " . $orgUnit . " started at " . date(TIME_FORMAT, $start) . "\n";
		$url = $baseURL . '/departments/' . $orgUnit . '/personnelsubtree?format=json&reset-cache';
		$ch = curl_init($url);
		$timeout = 800;

		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$result = curl_exec($ch);
		$end = time();
		$duration = ($end - $start) / 60;

		if ($result === FALSE) {
			echo "Curl error : " . curl_error($ch) . " at " . date(TIME_FORMAT, $end) . " and took " . round($duration, 3) . MINUTES_LINE_END;
		} else {
			$resultJSON = json_decode($result);
			$personnelCount = 0;
			if (!empty($resultJSON) && is_array($resultJSON) && count($resultJSON) >= 1 && is_object($resultJSON[0]) && isset($resultJSON[0]->dn)){
				$success = TRUE;
				$personnelCount = count($resultJSON);
				echo "SUCCESS (" . $personnelCount . " personnel): " . $orgUnit . " finished at " . date(TIME_FORMAT, $end) . " and took " . round($duration, 3) . MINUTES_LINE_END;
			} else {
				echo "FAILED: " . $orgUnit . " finished at " . date(TIME_FORMAT, $end) . " and took " . round($duration, 3) . MINUTES_LINE_END;
			}
		}
		curl_close($ch);
	}
}
