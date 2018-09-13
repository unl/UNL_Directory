<?php
require_once __DIR__.'/../www/config.inc.php';

$cache = UNL_Peoplefinder_Cache::factory();

// This is the array that will store all of the data
$students = array();

// First, load the bio info and create the initial structure
$csv_bio_file = __DIR__.'/../data/unl_sis_bio.txt';

if (!file_exists($csv_bio_file)) {
    // You must manually upload the file
    return;
}

$csv_bio_file = file_get_contents($csv_bio_file);
$csv_bio_file = mb_convert_encoding($csv_bio_file, 'UTF-8'); //See note 'Parsing export from drupal webforms
$csv_bio_rows = explode("\n", $csv_bio_file);

// Remove the first row, which should be the column headers and not actual data.
array_shift($csv_bio_rows);

foreach ($csv_bio_rows as $row) {
    /**
     * [0] = NUID
     * [1] = CLASS_LEVEL
     * 
     * ONLY non-ferpa protected records are included
     * 
     */
    $data = str_getcsv($row, ",", '"');
    
    if (!isset($data[1])) {
        // This is likely a blank line at the end of the file
        continue;
    }
    
    $students[$data[0]] = array(
        'unlsisclasslevel' => $data[1],
        'unlsiscollege' => array(),
        'unlsismajor' => array(),
        'unlsisminor' => array(), // Minor data is not shown (not listed as public directory information in the policy)
    );
}

// Second, load the college, major, and minor information and add it to the existing structure
$csv_prog_file = __DIR__.'/../data/unl_sis_prog.txt';

if (!file_exists($csv_prog_file)) {
    // You must manually upload the file
    return;
}

$csv_prog_file = file_get_contents($csv_prog_file);
$csv_prog_file = mb_convert_encoding($csv_prog_file, 'UTF-8'); //See note 'Parsing export from drupal webforms
$csv_prog_rows = explode("\n", $csv_prog_file);

// Remove the first row, which should be the column headers and not actual data.
array_shift($csv_prog_rows);

foreach ($csv_prog_rows as $row) {
    /**
     * [0] = NUID
     * [1] = INSTITUTION
     * [2] = ACAD_PROG (program code "ARH-U" for example)
     * [3] = PROG_DESCR (college)
     * [4] = PLAN_DESCR (major/minor name)
     * [5] = ACAD_PLAN_TYPE ('MAJ' => 'Major', 'MIN' => 'Minor', 'COS' => 'Course of study')
     */
    $data = str_getcsv($row, ",", '"');

    if (!isset($data[1])) {
        // This is likely a blank line at the end of the file
        continue;
    }

    if (!isset($students[$data[0]])) {
        // Student is not tracked, likely do to a FERPA flag
        continue;
    }

    $students[$data[0]]['unlsiscollege'][] = $data[2];
    // Make sure this element only contains unique values
    // (otherwise the same college might appear twice and things will look broken)
    $students[$data[0]]['unlsiscollege'] = array_unique($students[$data[0]]['unlsiscollege']);
    
    if (isset($data[5])) {
        $plan_type = strtolower(trim($data[5]));

        // Display Course of Study as a major per Steve Booton
        if ($plan_type === 'maj' || $plan_type === 'cos') {
            $students[$data[0]]['unlsismajor'][] = $data[4];
        } else {
            echo 'unknown plan type ' . $plan_type . PHP_EOL;
        }
    }
}

// Clear all old memcache results if needed
$existing_keys = $cache->get('unl_sis_keys');

// Save to Memcache
$prefix = 'unl_sis_';
$unl_sis_keys = array(); // Keep track of all existing keys because Memcached->getAllKeys() is not reliable
foreach ($students as $nuid => $data) {
    $cache_key = $prefix.$nuid;
    $unl_sis_keys[] = $cache_key;
    $cache->set($cache_key, serialize($data), false, true);
}
$cache->set('unl_sis_keys', serialize($unl_sis_keys), false, true);


if ($existing_keys) {
    $existing_keys = unserialize($existing_keys);
    foreach ($existing_keys as $key) {
        if (!in_array($key, $unl_sis_keys)) {
            // only remove keys that don't exist in the new data (avoid a brief moment when data might be unavailable)
            $cache->remove($key);
        }
    }
}
