<?php
// The web view is special.

// First, we group results by affiliation
// Second, we allow "Like" results to be displayed after the exact matches
$showing = 0;
$resultCount = count($context->results);
$deptResultCount = count($context->dept_results);

// The HTML view prefers to have them grouped by affiliation
$by_affiliation      = UNL_Peoplefinder_SearchResults::groupByAffiliation($context->getRaw('results'));
$like_by_affiliation = UNL_Peoplefinder_SearchResults::groupByAffiliation($context->getRaw('likeResults'));
$affiliations = array_keys($by_affiliation + $like_by_affiliation);
usort($affiliations, ['UNL_Peoplefinder_SearchResults', 'affiliationSort']);

$sections = [];
// We now have both the exact and like matches grouped by affiliation into special arrays.
foreach ($affiliations as $affiliation) {
    if (isset($by_affiliation[$affiliation]) || isset($like_by_affiliation[$affiliation])) {
        $section = [
            'affiliation' => $affiliation,
            'results' => [],
            'like_results' => [],
            'options' => $context->options
        ];

        if (isset($by_affiliation[$affiliation])) {
            $section['results'] = $by_affiliation[$affiliation];
        }

        if (isset($like_by_affiliation[$affiliation])) {
            $section['like_results'] = $like_by_affiliation[$affiliation];
        }

        // Remember to tally up what is actually showing
        $showing += count($section['results']);
        $showing += count($section['like_results']);

        $sections[] = $section;
    }
}
?>

<?php if ($resultCount >= UNL_Peoplefinder::$resultLimit): ?>
    <p>Your search could only return a subset of the results. Try refining your search.</p>
<?php endif; ?>

<?php if ($deptResultCount): ?>
    <?php echo $savvy->render($context->dept_results) ?>
<?php endif; ?>

<?php foreach ($sections as $section): ?>
    <?php echo $savvy->render((object) $section, 'Peoplefinder/SearchResults/ByAffiliation.tpl.php') ?>
<?php endforeach; ?>

<?php if (!$deptResultCount && !$showing): ?>
    Sorry, no results could be found.
<?php endif; ?>
