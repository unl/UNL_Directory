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

$dept1 = UNL_Officefinder_Department::getByID(1);
$dept1->setAsRoot();

$dept2 = UNL_Officefinder_Department::getByID(2);
$dept3 = UNL_Officefinder_Department::getByID(3);

$dept1->addChild($dept2);
$dept1->addChild($dept3);

echo "<br/>Has children?";
var_dump($dept1->hasChildren());


echo '<br/>Is child of?';
var_dump($dept2->isChildOf($dept1));

echo "<br/>The root!";
var_dump($dept2->getRoot()->name);

echo "<br/>The children!";
foreach ($dept1->getChildren() as $child) {
    echo $child->name;
}

echo "<br/>Get root's level";
var_dump($dept1->getLevel());

echo "<br/>dept2's level ";
var_dump($dept2->getLevel());
echo "<br/>dept3's level ";
var_dump($dept3->getLevel());

echo "<br/>dept3's parent name! ";
var_dump($dept3->getParent()->name);

echo "<br/>dept2's parent name! ";
var_dump($dept3->getParent()->name);
