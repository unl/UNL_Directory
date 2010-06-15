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
    if ($context->hasChildren()) {
        echo 'Sub-departments:<ul>';
        foreach ($context->getChildren() as $child) {
            echo '<li><a href="'.UNL_PEOPLEFINDER_URI.'departments/?d='.urlencode($child).'">'.htmlentities($child).'</a></li>';
        }
        echo '</ul>';
    }
    echo '<ul class="department">';
    foreach ($context as $employee) {
        echo '<li class="ppl_Sresult">';
        echo $savvy->render($employee);
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo 'No results could be found.';
}