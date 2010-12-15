<?php

$like_records = array();
if (!is_array($context->options['q'])
    && UNL_Peoplefinder::$displayResultLimit) {
    // More room to display like results
    $like_records = $context->options['peoplefinder']->getLikeMatches($context->options['q'], null, $context->results);
}

echo $savvy->render($context->results);

if ($like_records) {
    echo $savvy->render($like_records);
}