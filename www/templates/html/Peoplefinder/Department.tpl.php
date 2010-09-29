<?php
if (count($context)) {
    echo '<h2>'.$context->name.'</h2>';
    $buildinig = '';
    if (isset($context->building)) {
        $building = $context->building;
        $bldgs = new UNL_Common_Building();
        if ($bldgs->buildingExists($context->building)) {
            $sd = new UNL_Geography_SpatialData_Campus();
            $building = '<a href="'.$sd->getMapUrl($context->building).'">'.htmlentities($bldgs->codes[$context->building]).'</a>';
        }
        $building = "<span class='location'>$building</span>";
    }
    echo "<p>{$context->room} {$building}<br />{$context->city}, {$context->state} {$context->postal_code}</p>";
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