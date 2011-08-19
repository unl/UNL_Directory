<?php
if (count($context)) {
    echo '<h3>'.$context->name.'</h3>';
    $building = '';
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
    echo $savvy->render($context, 'Peoplefinder/Department/Personnel.tpl.php');
} else {
    echo 'No results could be found.';
}