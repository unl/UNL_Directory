<?php
echo '<div id="all_employees">';
$by_affiliation = UNL_Peoplefinder_SearchResults::groupByAffiliation($context->getRawObject());

// Hide various affilations
if (isset($by_affiliation['student'])) {
    unset($by_affiliation['student']);
}

ksort($by_affiliation);
foreach ($by_affiliation as $affiliation=>$records) {
    if (count($records)) {
        $section               = new stdClass();
        $section->affiliation  = $affiliation;
        $section->results      = $records;
        $section->like_results = array();
        $section->options      = $context->options;
        echo $savvy->render($section, 'Peoplefinder/SearchResults/ByAffiliation.tpl.php');
    }
}
echo '</div>';