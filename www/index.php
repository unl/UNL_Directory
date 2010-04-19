<?php
require_once 'config.inc.php';

$options = $_GET;
$options['driver'] = $driver;
$peoplefinder  = new UNL_Peoplefinder($options);


Savvy_ClassToTemplateMapper::$classname_replacement = 'UNL_';
$savvy = new Savvy();
$savvy->setTemplatePath(dirname(__FILE__).'/templates/html');


//if ($peoplefinder->options['format'] != 'html') {
//    switch($enews->options['format']) {
//        case 'json':
//            $savvy->addTemplatePath(dirname(__FILE__).'/templates/'.$enews->options['format']);
//            break;
//        default:
//    }
//}

echo $savvy->render($peoplefinder);

