<?php
ini_set('display_errors', true);
error_reporting(E_ALL|E_STRICT);

require_once __DIR__ . '/../vendor/autoload.php';

// Set the main URL for the site
UNL_Peoplefinder::$url = '/';

//Set the annotation service's URL
UNL_Peoplefinder::$annotateUrl = 'https://annotate.unl.edu/';

UNL_Peoplefinder::$staticFileVersion = '81a5098';

// If you have LDAP access credentials, best to use this driver, using your credentials
UNL_Peoplefinder_Driver_LDAP::$bindDN = 'uid=giggidy,ou=service,dc=unl,dc=edu';
UNL_Peoplefinder_Driver_LDAP::$bindPW = 'flibbertygibberty';
$driver = new UNL_Peoplefinder_Driver_LDAP();

// Otherwise, use the webservice driver
//$driver = new UNL_Peoplefinder_Driver_WebService();

// Database connection info for officefinder portions
UNL_Officefinder::$db_host = 'localhost';
UNL_Officefinder::$db_user = 'officefinder';
UNL_Officefinder::$db_pass = 'officefinder';
UNL_Officefinder_CorrectionEmail::$defaultRecipient = 'nobody@unl.edu';

// Officefinder editing admins
UNL_Officefinder::$admins = array('hhusker1');

UNL_Knowledge_Driver_REST::$service_user = 'unl/web_service_unlwebcv';
UNL_Knowledge_Driver_REST::$service_pass = 'examplepassword';

# set the memcache host and port
UNL_Knowledge_Driver_REST::$memcache_host = 'localhost';
UNL_Knowledge_Driver_REST::$memcache_port = 11211;

UNL_Peoplefinder_Driver_OracleDB::$connection_username = 'USER';
UNL_Peoplefinder_Driver_OracleDB::$connection_password = 'PASS';
UNL_Peoplefinder_Driver_OracleDB::$connection_host = '1.2.3.4';
UNL_Peoplefinder_Driver_OracleDB::$connection_port = 1234;
UNL_Peoplefinder_Driver_OracleDB::$connection_service = "SAPTPRD";

// Test domains used in Peoplefinder.tpl.php
UNL_Peoplefinder::$testDomains = array('directory-test.unl.edu', 'localhost');

// Sample user ID
//UNL_Peoplefinder::$sampleUID = 'hhusker1';
//include_once __DIR__ . '/../data/test-data.inc.php';

// Site Notice
$siteNotice = new stdClass();
$siteNotice->display = false;
$siteNotice->noticePath = 'dcf-notice';
$siteNotice->containerID = 'dcf-main';
$siteNotice->type = 'dcf-notice-info';
$siteNotice->title = 'Maintenance Notice';
$siteNotice->message = 'We will be performing site maintenance on February 3rd from 4:30 to 5:00.  The site may not be available during this time.';
global $siteNotice;
