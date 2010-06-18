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
    $i = 0;
    echo '<ul class="department pfResult">';
    foreach ($context as $employee) {
        $even_odd = ($i % 2) ? '' : 'alt';
        if ($employee->ou == 'org') {
            $class = 'org_Sresult';
        } else {
            $class = 'ppl_Sresult';
        }
        $class .= ' '.$employee->eduPersonPrimaryAffiliation;
        echo '<li class="'.$class.' '.$even_odd.'"><div class="overflow">';
        echo $savvy->render($employee, 'Peoplefinder/RecordInList.tpl.php');
        echo '</div></li>'.PHP_EOL;
        $i++;
    }
    echo '</ul>';
} else {
    echo 'No results could be found.';
}