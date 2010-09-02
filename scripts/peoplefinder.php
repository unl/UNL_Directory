#!/usr/bin/env php
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

$options           = UNL_Peoplefinder_CLI_Router::route();
$options['driver'] = new UNL_Peoplefinder_Driver_WebService();
$peoplefinder      = new UNL_Peoplefinder($options);

Savvy_ClassToTemplateMapper::$classname_replacement = 'UNL_';
$savvy = new Savvy();
$savvy->setTemplatePath(dirname(dirname(__FILE__)).'/data/cli');

echo $savvy->render($peoplefinder);

?>