<?php
ini_set('display_errors', true);
error_reporting(E_ALL|E_STRICT);
require_once 'UNL/Autoload.php';

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);

UNL_Peoplefinder_Driver_LDAP::$bindDN = 'uid=giggidy,ou=service,dc=unl,dc=edu';
UNL_Peoplefinder_Driver_LDAP::$bindPW = 'flibbertygibberty';

define('UNL_PEOPLEFINDER_URI', 'http://peoplefinder.unl.edu/');
set_time_limit(5);
$driver = new UNL_Peoplefinder_Driver_LDAP();