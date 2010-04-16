<?php
ini_set('display_errors',true);

chdir(dirname(__FILE__));
set_include_path(dirname(dirname(__FILE__)).':/usr/local/php5/lib/php');

require_once 'UNL/Autoload.php';

$pf = new UNL_Peoplefinder(new UNL_Peoplefinder_Driver_WebService());

$sav = new UNL_Savant();
echo $sav->render($pf);
?>