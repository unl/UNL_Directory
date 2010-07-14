<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>UNL_Geography_SpatialData_Campus</title>
</head>

<body>
<p>This file demonstrates the usage of the UNL_Geography_SpatialData_Campus package.</p>
<?php

require_once dirname(__FILE__).'/../Campus.php';
//require_once 'UNL/Geography/SpatialData/Campus.php';
require_once 'UNL/Common/Building.php';

$bldgs = new UNL_Common_Building();
$campus = new UNL_Geography_SpatialData_Campus();

foreach (array('NH','501') as $bldg_code) {
	$geoCoordinates = $campus->getGeoCoordinates($bldg_code);
	echo "<p>The building, {$bldgs->codes[$bldg_code]} ($bldg_code) is located at lat:{$geoCoordinates['lat']} lon:{$geoCoordinates['lon']}</p>";
	echo '<ul>';
	echo "<li><a href='http://maps.google.com/?t=k&amp;ll={$geoCoordinates['lat']},{$geoCoordinates['lon']}&amp;spn=0.001212,0.002427&amp;om=1'>Google Map of this</a></li>";
	echo "<li><a href='http://maps.yahoo.com/beta/index.php#maxp=search&amp;mvt=s&amp;trf=0&amp;lon={$geoCoordinates['lon']}&lat={$geoCoordinates['lat']}&amp;mag=1'>Yahoo! Map of this</a></li>";
	echo '</ul>';
	echo '<a href="#" onclick="document.getElementById(\'source\').style.display=\'block\'; return false;">View Source+</a><div id="source" style="display:none;">'.highlight_file($_SERVER['SCRIPT_FILENAME'],true).'</div>';
}

?>
</body>
</html>