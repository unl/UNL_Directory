<?php
if ($context->options['format'] != 'partial') {
    if (isset($context->options['adv'])) {
        echo $savvy->render($context, 'Peoplefinder/AdvancedForm.tpl.php');
    } else {
        echo $savvy->render($context, 'Peoplefinder/StandardForm.tpl.php');
    }
}

// The web view is special.

// First, we group results by affiliation
// Second, we allow "Like" results to be displayed after the exact matches

UNL_Peoplefinder::$displayResultLimit -= count($context->results);

if (count($context->dept_results)) {
    echo $savvy->render($context->dept_results);
}

$like_records = array();
if (!is_array($context->options['q'])
    && UNL_Peoplefinder::$displayResultLimit) {
    // More room to display like results
    $like_records = $context->options['peoplefinder']->getLikeMatches($context->options['q'], null, $context->results);
}


// The HTML view prefers to have them grouped by affiliation
$showing = count($context->results) + count($like_records);

$by_affiliation      = UNL_Peoplefinder_SearchResults::groupByAffiliation($context->getRaw('results'));
$like_by_affiliation = UNL_Peoplefinder_SearchResults::groupByAffiliation($like_records);

// We now have both the exact and like matches grouped by affiliation into special arrays.

foreach ($by_affiliation as $affiliation=>$records) {
    if (count($records)
        || count($like_by_affiliation[$affiliation])) {
        $section               = new stdClass();
        $section->affiliation  = $affiliation;
        $section->results      = $records;
        $section->like_results = $like_by_affiliation[$affiliation];
        $section->options      = $context->options;
        echo $savvy->render($section, 'Peoplefinder/SearchResults/ByAffiliation.tpl.php');
    }
}

if (count($context->dept_results) == 0
    && $showing == 0) {
    echo 'Sorry, no results could be found.';
}

?>