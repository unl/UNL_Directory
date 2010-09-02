<?php
foreach ($context as $record) {
    echo '______________________________'.PHP_EOL;
    if ($record instanceof UNL_Peoplefinder_Record) {
        echo $savvy->render($record, 'Peoplefinder/RecordInList.tpl.php');
    } else {
        echo $savvy->render($record);
    }
}