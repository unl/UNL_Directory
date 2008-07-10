<?php
ini_set('display_errors', true);
error_reporting(E_ALL|E_STRICT);
require_once 'UNL/Peoplefinder.php';

UNL_Peoplefinder::$bindDN = 'uid=giggidy,ou=service,dc=unl,dc=edu';
UNL_Peoplefinder::$bindPW = 'flibbertygibberty';

define('UNL_PEOPLEFINDER_URI', 'http://peoplefinder.unl.edu/');
