<?php
require_once 'config.inc.php';

$options = array();
if (strpos($_SERVER['REQUEST_URI'], 'service.php') !== false) {
    $options = array('format'=>'partial', 'onclick'=>'pf_getUID');
}

$options = $_GET + $options;
$options['driver'] = $driver;

if (strpos($_SERVER['REQUEST_URI'], '/departments/') === false) {
    $peoplefinder  = new UNL_Peoplefinder($options);
} else {
    $peoplefinder  = new UNL_Officefinder($options);
}

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
$savvy = new Savvy();
$savvy->setTemplatePath(__DIR__.'/templates/html');

switch($peoplefinder->options['format']) {
    case 'vcard':
        header('Content-Type: text/x-vcard');
        $savvy->addTemplatePath(__DIR__.'/templates/vcard');
        break;
    case 'json':
        header('Content-Type: application/json');
        $savvy->addTemplatePath(__DIR__.'/templates/json');
        break;
    case 'php':
        header('Content-Type: text/plain');
        $savvy->addTemplatePath(__DIR__.'/templates/php');
        break;
    case 'xml':
        header('Content-type: text/xml');
        $savvy->setEscape('htmlspecialchars');
        $savvy->addTemplatePath(__DIR__.'/templates/xml');
        break;
    case 'partial':
    case 'hcard':
        Savvy_ClassToTemplateMapper::$output_template['UNL_Officefinder'] = 'Peoplefinder-partial';
        Savvy_ClassToTemplateMapper::$output_template['UNL_Peoplefinder'] = 'Peoplefinder-partial';
        // intentional no break
    case 'html':
    default:
        $savvy->setEscape('htmlspecialchars');
        $savvy->setHTMLEscapeSettings(['quotes' => ENT_QUOTES | ENT_HTML5]);
        if ($peoplefinder->options['view'] != 'alphalisting') {
            $savvy->addFilters(array('UNL_Peoplefinder', 'postRun'));
        }
}

$savvy->addGlobal('controller', $peoplefinder);

echo $savvy->render($peoplefinder);
