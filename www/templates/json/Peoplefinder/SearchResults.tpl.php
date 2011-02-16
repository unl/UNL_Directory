[<?php
$results = array();
foreach ($context as $record) {
    $results[] = $savvy->render($record);
}
echo implode(',', $results);
?>
]
