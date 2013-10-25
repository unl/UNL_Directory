<?php
require_once dirname(__DIR__) . '/config.inc.php';

$options = array();
if (strpos($_SERVER['REQUEST_URI'], 'service.php') !== false) {
    $options = array('format'=>'partial', 'onclick'=>'pf_getUID');
}

$options = $_GET + $options;
$options['driver'] = $driver;
$peoplefinder  = new UNL_Officefinder($options);

// Specify domains from which requests are allowed
header('Access-Control-Allow-Origin: *');

// Specify which request methods are allowed
header('Access-Control-Allow-Methods: GET, OPTIONS');

// Additional headers which may be sent along with the CORS request
// The X-Requested-With header allows jQuery requests to go through
header('Access-Control-Allow-Headers: X-Requested-With');

// Exit early so the page isn't fully loaded for options requests
if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
    exit();
}

Savvy_ClassToTemplateMapper::$classname_replacement = 'UNL_';
$savvy = new Savvy_Turbo();
$savvy->setTemplatePath(dirname(dirname(__FILE__)).'/templates/html');

switch($peoplefinder->options['format']) {
    case 'vcard':
        if ($peoplefinder->output[0] instanceof UNL_Peoplefinder_Record) {
            header('Content-Type: text/x-vcard');
            header('Content-Disposition: attachment; filename="'.$peoplefinder->output[0]->sn.', '.$peoplefinder->output[0]->givenName.'.vcf"');
        }
        //intentional no break
    case 'json':
    case 'php':
    case 'xml':
        $savvy->addTemplatePath(dirname(dirname(__FILE__)).'/templates/'.$peoplefinder->options['format']);
        break;
    case 'editing':
        $savvy->setEscape('htmlentities');
        $savvy->addTemplatePath(dirname(dirname(__FILE__)).'/templates/'.$peoplefinder->options['format']);
        break;
    case 'partial':
    case 'hcard':
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

$savvy->addGlobal('controller', $peoplefinder);

echo $savvy->render($peoplefinder);