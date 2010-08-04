<?php
if (count($context)) {
    echo count($context).' results.';
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
    $i = 0;
    echo '<ul class="department pfResult">'.PHP_EOL;
    foreach ($context as $employee) {
        echo $savvy->render($employee, 'Peoplefinder/RecordInList.tpl.php');
    }
    echo '</ul>'.PHP_EOL;
} else {
    echo 'No results could be found.';
}