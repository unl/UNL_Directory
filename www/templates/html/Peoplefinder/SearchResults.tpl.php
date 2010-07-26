<?php


$start    = 0;
$num_rows = UNL_PF_DISPLAY_LIMIT;

if (($start+$num_rows)>count($context)) {
    $end = count($context);
} else {
    $end = $start+$num_rows;
}

echo "<div class='result_head'>Results ".($start+1)." - $end out of ".count($context).'</div>'.PHP_EOL;
echo '<ul class="pfResult">'.PHP_EOL; //I need to put a class for CSS, however when we switch to chuncked results (student, staff, faculty) this @todo will need revisted
for ($i = $start; $i<$end; $i++) {
    if ($context[$i] instanceof UNL_Peoplefinder_Record) {
        echo $savvy->render($context[$i], 'Peoplefinder/RecordInList.tpl.php');
    } else {
        echo $savvy->render($context[$i]);
    }
}
echo '</ul>'.PHP_EOL;

if (count($context) >= UNL_Peoplefinder::$resultLimit) {
    echo "<p>Your search could only return a subset of the results. ";
    if (isset($context->options['adv'])
        && $context->options['adv'] != 'y') {
        echo "Would you like to <a href='".UNL_Peoplefinder::getURL()."?adv=y' title='Click here to perform a detailed Peoplefinder search'>try a Detailed Search?</a>\n";
    } else {
        echo 'Try refining your search.';
    }
    echo '</p>';
}

?>