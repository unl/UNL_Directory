<?php
require_once 'config.inc.php';

$options = $_GET;
$options['driver'] = $driver;
$peoplefinder  = new UNL_Peoplefinder($options);


Savvy_ClassToTemplateMapper::$classname_replacement = 'UNL_';
$savvy = new Savvy();
$savvy->setTemplatePath(dirname(__FILE__).'/templates/html');


if ($peoplefinder->options['format'] != 'html') {
    switch($peoplefinder->options['format']) {
        case 'json':
            $savvy->addTemplatePath(dirname(__FILE__).'/templates/'.$peoplefinder->options['format']);
            break;
        case 'hcard':
            Savvy_ClassToTemplateMapper::$output_template['UNL_Peoplefinder'] = 'Peoplefinder-partial';
        default:
    }
}

echo $savvy->render($peoplefinder);

