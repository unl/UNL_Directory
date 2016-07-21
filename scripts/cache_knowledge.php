<?php
require_once 'www/config.inc.php';

$driver = new UNL_Knowledge_Driver_REST;
$driver->getAllRecords();