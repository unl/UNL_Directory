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

// Set the main URL for the site
UNL_Peoplefinder::$url = 'http://peoplefinder.unl.edu/';

// Some LDAP queries take a long time, change this if necessary
set_time_limit(5);

// If you have LDAP access credentials, best to use this driver, using your credentials
UNL_Peoplefinder_Driver_LDAP::$bindDN = 'uid=giggidy,ou=service,dc=unl,dc=edu';
UNL_Peoplefinder_Driver_LDAP::$bindPW = 'flibbertygibberty';
$driver = new UNL_Peoplefinder_Driver_LDAP();

// Otherwise, use the webservice driver
$driver = new UNL_Peoplefinder_Driver_WebService(array('service_url'=>'http://ucommbieber.unl.edu/workspace/peoplefinder/www/service.php'));

// Database connection info for officefinder portions
UNL_Officefinder::$db_user = 'officefinder';
UNL_Officefinder::$db_pass = 'officefinder';

// Officefinder editing admins
UNL_Officefinder::$admins = array('bbieber2', 'smeranda2', 'erasmussen2');

