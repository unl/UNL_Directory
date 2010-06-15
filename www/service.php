<?php
/**
 * This page provides the peoplefinder service to applications.
 *
 */

require_once 'config.inc.php';

// Specify domains from which requests are allowed
header('Access-Control-Allow-Origin: *');

// Specify which request methods are allowed
header('Access-Control-Allow-Methods: GET, OPTIONS');

// Additional headers which may be sent along with the CORS request
// The X-Requested-With header allows jQuery requests to go through
header('Access-Control-Allow-Headers: X-Requested-With');

// Set the ages for the access-control header to 20 days to improve speed/caching.
header('Access-Control-Max-Age: 1728000');

// Set expires header for 24 hours to improve speed caching.
header('Expires: '.date('r', strtotime('tomorrow')));

// Exit early so the page isn't fully loaded for options requests
if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
    exit();
}
$options = array('format'=>'partial', 'onclick'=>'pf_getUID');
$options = $_GET + $options;
$options['driver'] = $driver;
$peoplefinder  = new UNL_Peoplefinder($options);


Savvy_ClassToTemplateMapper::$classname_replacement = 'UNL_';
$savvy = new Savvy();
$savvy->setTemplatePath(dirname(__FILE__).'/templates/html');


if ($peoplefinder->options['format'] != 'html') {
    switch($peoplefinder->options['format']) {
        case 'json':
        case 'php':
        case 'vcard':
        case 'xml':
            $savvy->addTemplatePath(dirname(__FILE__).'/templates/'.$peoplefinder->options['format']);
            break;
        case 'hcard':
        case 'partial':
            Savvy_ClassToTemplateMapper::$output_template['UNL_Peoplefinder'] = 'Peoplefinder-partial';
        default:
    }
}

echo $savvy->render($peoplefinder);
