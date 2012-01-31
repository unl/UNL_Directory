<?php
require_once dirname(__FILE__).'/../config.inc.php';

$options = $_GET;
$options['driver'] = $driver;
$peoplefinder  = new UNL_Officefinder($options);

// Specify domains from which requests are allowed
header('Access-Control-Allow-Origin: *');

// Specify which request methods are allowed
header('Access-Control-Allow-Methods: GET, OPTIONS');

// Additional headers which may be sent along with the CORS request
// The X-Requested-With header allows jQuery requests to go through
header('Access-Control-Allow-Headers: X-Requested-With');

Savvy_ClassToTemplateMapper::$classname_replacement = 'UNL_';
$savvy = new Savvy_Turbo();
$savvy->setTemplatePath(dirname(dirname(__FILE__)).'/templates/html');

switch($peoplefinder->options['format']) {
    case 'json':
    case 'php':
    case 'vcard':
    case 'xml':
        $savvy->addTemplatePath(dirname(dirname(__FILE__)).'/templates/'.$peoplefinder->options['format']);
        break;
    case 'editing':
        $savvy->setEscape('htmlentities');
        $savvy->addTemplatePath(dirname(dirname(__FILE__)).'/templates/'.$peoplefinder->options['format']);
        break;
    case 'hcard':
    case 'partial':
        Savvy_ClassToTemplateMapper::$output_template['UNL_Officefinder'] = 'Peoplefinder-partial';
        // intentional no break
    default:
        $savvy->setEscape('htmlentities');
        break;
    case array('editing', 'partial'):
    case array('partial', 'editing'):
        $savvy->setEscape('htmlentities');
        $savvy->addTemplatePath(dirname(dirname(__FILE__)).'/templates/editing');
        Savvy_ClassToTemplateMapper::$output_template['UNL_Officefinder'] = 'Peoplefinder-partial';
        break;
}

if ($peoplefinder->options['view'] != 'alphalisting') {
    $savvy->addFilters(array('UNL_Officefinder', 'postRun'));
}

echo $savvy->render($peoplefinder);