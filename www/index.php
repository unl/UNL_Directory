<?php
require_once 'config.inc.php';

$options = $_GET;
$options['driver'] = $driver;
$peoplefinder  = new UNL_Peoplefinder($options);


Savvy_ClassToTemplateMapper::$classname_replacement = 'UNL_';
$savvy = new Savvy();
$savvy->setTemplatePath(dirname(__FILE__).'/templates/html');


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
        $savvy->addTemplatePath(dirname(__FILE__).'/templates/'.$peoplefinder->options['format']);
        break;
    case 'partial':
    case 'hcard':
        Savvy_ClassToTemplateMapper::$output_template['UNL_Peoplefinder'] = 'Peoplefinder-partial';
        // intentional no break
    case 'html':
    default:
}

$savvy->setEscape('htmlentities');

echo $savvy->render($peoplefinder);

