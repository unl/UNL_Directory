<?php
ini_set('display_errors', true);
error_reporting(E_ALL|E_STRICT);

require_once '../vendor/autoload.php';

// Set the main URL for the site
UNL_Peoplefinder::$url = '/';

//Set the annotation service's URL
UNL_Peoplefinder::$annotateUrl = 'http://annotate.unl.edu/';

// Some LDAP queries take a long time, change this if necessary
set_time_limit(5);

// If you have LDAP access credentials, best to use this driver, using your credentials
UNL_Peoplefinder_Driver_LDAP::$bindDN = 'uid=giggidy,ou=service,dc=unl,dc=edu';
UNL_Peoplefinder_Driver_LDAP::$bindPW = 'flibbertygibberty';
$driver = new UNL_Peoplefinder_Driver_LDAP();

// Otherwise, use the webservice driver
$driver = new UNL_Peoplefinder_Driver_WebService();

// Database connection info for officefinder portions
UNL_Officefinder::$db_user = 'officefinder';
UNL_Officefinder::$db_pass = 'officefinder';

// Officefinder editing admins
UNL_Officefinder::$admins = array('bbieber2', 'smeranda2', 'erasmussen2');

