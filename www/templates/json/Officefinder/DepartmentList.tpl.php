<?php
$depts = array();

foreach ($context as $department) {
    $depts[] = $savvy->render($department);
}

echo implode(',', $depts);
