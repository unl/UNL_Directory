<?php
ini_set('display_errors', true);
error_reporting(E_ALL|E_STRICT);

function autoload($class)
{
    $class = str_replace('_', '/', $class);
    include $class . '.php';
}
    
spl_autoload_register("autoload");


set_include_path(dirname(dirname(__FILE__)).'/src/'.PATH_SEPARATOR.dirname(dirname(__FILE__)).'/lib/php');
require_once 'UNL/Autoload.php';

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);

UNL_Peoplefinder_Driver_LDAP::$bindDN = 'uid=giggidy,ou=service,dc=unl,dc=edu';
UNL_Peoplefinder_Driver_LDAP::$bindPW = 'flibbertygibberty';

UNL_Peoplefinder::$url = 'http://peoplefinder.unl.edu/';
set_time_limit(5);

// If you have LDAP access credentials, best to use this driver
$driver = new UNL_Peoplefinder_Driver_LDAP();

// Otherwise, use the webservice driver
$driver = new UNL_Peoplefinder_Driver_WebService(array('service_url'=>'http://ucommbieber.unl.edu/workspace/peoplefinder/www/service.php'));

/*
//database connection info
UNL_Officefinder::$db_user = '';
UNL_Officefinder::$db_pass = '';
UNL_Officefinder::$admins = array('bbieber2', 'smeranda', 'erasmussen2');
*/
