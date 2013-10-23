<?php

UNL_Peoplefinder::$displayResultLimit -= count($context->results);

$like_records = array();
if (!($context->options['q'] instanceof ArrayAccess)
    && !isset($context->options['method'])
    && UNL_Peoplefinder::$displayResultLimit) {
    // More room to display like results
    $like_records = $context->getRawObject()->options['peoplefinder']->getLikeMatches($context->options['q'], null, $context->results);
}

echo $savvy->render($context->results);

if ($like_records) {
    echo $savvy->render($like_records);
}