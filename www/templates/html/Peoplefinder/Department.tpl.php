<?php
if (count($context)) {
    echo '<h2>'.htmlentities($context->name).'</h2>';
    if (isset($context->building)) {
        $bldgs = new UNL_Common_Building();
        if ($bldgs->buildingExists($context->building)) {
            $sd = new UNL_Geography_SpatialData_Campus();
            $context->building = '<a href="'.$sd->getMapUrl($context->building).'">'.htmlentities($bldgs->codes[$context->building]).'</a>';
        }
    }
    echo "<p>{$context->room} <span class='location'>{$context->building}</span><br />{$context->city}, {$context->state} {$context->postal_code}</p>";
//    if ($context->hasChildren()) {
//        echo 'Sub-departments:<ul>';
//        foreach ($context->getChildren() as $child) {
//            echo '<li><a href="'.UNL_Officefinder::getURL().'?d='.urlencode($child->name).'">'.htmlentities($child->name).'</a></li>';
//        }
//        echo '</ul>';
//    }
    $by_affiliation = UNL_Peoplefinder_SearchResults::groupByAffiliation($context->getRawObject());
    foreach ($by_affiliation as $affiliation=>$records) {
        if (count($records)) {
            $section               = new stdClass();
            $section->affiliation  = $affiliation;
            $section->results      = $records;
            $section->like_results = array();
            echo $savvy->render($section, 'Peoplefinder/SearchResults/ByAffiliation.tpl.php');
        }
    }
} else {
    echo 'No results could be found.';
}