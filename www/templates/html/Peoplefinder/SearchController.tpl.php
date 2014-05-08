<?php
if ($context->options['format'] != 'partial') {
    ?>
    <div id="searchform" class="wdn-band wdn-light-neutral-band search-container">
        <div class="wdn-inner-wrapper wdn-inner-padding-sm">
            <?php
            if (isset($context->options['adv'])) {
                echo $savvy->render($context, 'Peoplefinder/AdvancedForm.tpl.php');
            } else {
                echo $savvy->render($context, 'Peoplefinder/StandardForm.tpl.php');
            }
            ?>
        </div>
    </div>
    <div class="wdn-band results-container">
        <div class="wdn-inner-wrapper">
            <div class="wdn-grid-set">
                <div class="bp2-wdn-col-one-fourth">
                    <?php echo $savvy->render(null, 'Peoplefinder/SearchResults/Filters.tpl.php'); ?>
                </div>
                <div id="results" class="bp2-wdn-col-three-fourths">
    <?php
}

// The web view is special.

// First, we group results by affiliation
// Second, we allow "Like" results to be displayed after the exact matches

$showing = 0;

UNL_Peoplefinder::$displayResultLimit -= count($context->results);

if (count($context->dept_results)) {
    echo $savvy->render($context->dept_results);
}

$like_records = array();
if (!($context->options['q'] instanceof ArrayAccess)
    && UNL_Peoplefinder::$displayResultLimit) {
    // More room to display like results
    $like_records = $controller->getLikeMatches($context->options['q'], null, $context->getRaw('results'))->getRawObject();
}


// The HTML view prefers to have them grouped by affiliation

$by_affiliation      = UNL_Peoplefinder_SearchResults::groupByAffiliation($context->getRaw('results'));
$like_by_affiliation = UNL_Peoplefinder_SearchResults::groupByAffiliation($like_records);

// We now have both the exact and like matches grouped by affiliation into special arrays.

$affiliations = array_keys($by_affiliation + $like_by_affiliation);

usort($affiliations, array('UNL_Peoplefinder_SearchResults', 'affiliationSort'));

foreach ($affiliations as $affiliation) {
    if (isset($by_affiliation[$affiliation])
        || isset($like_by_affiliation[$affiliation])) {
        $section               = new stdClass();
        $section->affiliation  = $affiliation;
        $section->results      = array();
        if (isset($by_affiliation[$affiliation])) {
            $section->results = $by_affiliation[$affiliation];
        }
        $section->like_results = array();
        if (isset($like_by_affiliation[$affiliation])) {
            $section->like_results = $like_by_affiliation[$affiliation];
        }

        // Remember to tally up what is actually showing
        $showing += count($section->results);
        $showing += count($section->like_results);

        $section->options = $context->options;
        echo $savvy->render($section, 'Peoplefinder/SearchResults/ByAffiliation.tpl.php');
    }
}

if (count($context->dept_results) == 0
    && $showing == 0) {
    echo 'Sorry, no results could be found.';
}


if ($context->options['format'] != 'partial') {
    ?>
    </div></div></div></div>
    <?php
}

?>