<?php

$start = 0;
$end   = count($context);

if ($end > $start) {
    echo "<div class='result_head'>Results ".($start+1)." - $end</div>".PHP_EOL;
} else {
    echo "<div class='result_head'>No Results</div>".PHP_EOL;
}

echo '<ul class="pfResult">'.PHP_EOL; //I need to put a class for CSS, however when we switch to chuncked results (student, staff, faculty) this @todo will need revisted
foreach ($context as $record) {
    if ($record->getRawObject() instanceof UNL_Peoplefinder_Record) {
        echo $savvy->render($record, 'Peoplefinder/RecordInList.tpl.php');
    } else {
        echo $savvy->render($record);
    }
}
echo '</ul>'.PHP_EOL;
