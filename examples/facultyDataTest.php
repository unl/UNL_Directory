<?php
require_once dirname(__FILE__) . '/../www/config.inc.php';

$person = new UNL_Peoplefinder_Person(array('uid' => 'lperez1', 'peoplefinder' => NULL));

var_dump($person);