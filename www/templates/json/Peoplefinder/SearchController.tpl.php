<?php

$all_results = array();

foreach ($context->results as $result) {
    $all_results[] = $savvy->render($result);
}


foreach ($context->likeResults as $result) {
    $all_results[] = $savvy->render($result);
}

echo '['.implode(',', $all_results).']';
