<?php

require_once dirname(__FILE__).'/../www/config.inc.php';


$college_ids = array(
        55,85,60,144,121,139,75,70,181
        );

foreach ($college_ids as $id) {
    $dept = UNL_Officefinder_Department::getById($id);
    $depth = 0;
    if ($dept) {
        showChildren($dept, $depth);
    }
}

/**
 * 
 * @param UNL_Officefinder_Department $dept
 * @param int $depth
 */
function showChildren($dept, &$depth) {
    echo str_repeat(' ', $depth).$dept->name.': '.$dept->website.PHP_EOL;
    $depth++;
    foreach ($dept->getOfficialChildDepartments() as $child) {
        showChildren($child, $depth);
    }
    $depth--;
}