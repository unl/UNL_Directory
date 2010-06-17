<?php
if ($context->options['format'] != 'partial') {
    echo $savvy->render($context, 'StandardForm.tpl.php');
}

// The web view is special.

// First, we group results by affiliation
// Second, we allow "Like" results to be displayed after the exact matches

UNL_Peoplefinder::$displayResultLimit -= count($context->results);

$like_records = array();
if (!is_array($context->options['q'])
    && UNL_Peoplefinder::$displayResultLimit) {
    // More room to display like results
    $like_records = $context->options['peoplefinder']->getLikeMatches($context->options['q'], null, $context->results);
}


// The HTML view prefers to have them grouped by affiliation
$by_affiliation = array();
$by_affiliation['faculty']       = array();
$by_affiliation['staff']         = array();
$by_affiliation['student']       = array();
$by_affiliation['organizations'] = array();

$like_by_affiliation = $by_affiliation;

$records =& $context->results;

foreach(array('records'=>'by_affiliation', 'like_records'=>'like_by_affiliation') as $records_var=>$affiliation_var) {
    foreach ($$records_var as $record) {
        foreach ($record->ou as $ou) {
            if ($ou == 'org') {
                ${$affiliation_var}['organizations'][] = $record;
                break;
            }
        }

        if (isset($record->eduPersonAffiliation)) {
            foreach ($record->eduPersonAffiliation as $affiliation) {
                ${$affiliation_var}[$affiliation][] = $record;
            }
        }
    }
}

// We now have both the exact and like matches grouped by affiliation into special arrays.

foreach ($by_affiliation as $affiliation=>$records) {
    if (count($records)
        || count($like_by_affiliation[$affiliation])) {
        echo '<div class="affiliation '.$affiliation.'">';
        echo '<h2>'.ucfirst($affiliation).'</h2>';
        echo $savvy->render(new UNL_Peoplefinder_SearchResults(array('results'=>$records)));
        if (count($like_by_affiliation[$affiliation])) {
            echo '<div class="likeResults">';
            echo '<h3>similar '.$affiliation.' results</h3>';
            echo $savvy->render(new UNL_Peoplefinder_SearchResults(array('results'=>$like_by_affiliation[$affiliation])));
            echo '</div>';
        }
        echo '</div>';
    }
}
?>