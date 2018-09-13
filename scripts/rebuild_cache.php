<?php
require_once __DIR__.'/../www/config.inc.php';

ini_set('memory_limit','512M');

$cache = UNL_Peoplefinder_Cache::factory();

// Clear the existing cache
$cache->flush();

// Import SIS data
require_once __DIR__ . '/import_sis_data.php';

// Cache faculty data
$driver = new UNL_Knowledge_Driver_REST;
$driver->getAllRecords();
