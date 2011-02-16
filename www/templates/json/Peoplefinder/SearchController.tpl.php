<?php

UNL_Peoplefinder::$displayResultLimit -= count($context->results);

$like_records = array();
if (!is_array($context->options['q'])
    && !isset($context->options['method'])
    && UNL_Peoplefinder::$displayResultLimit) {
    // More room to display like results
    $like_records = $context->options['peoplefinder']->getLikeMatches($context->options['q'], null, $context->results);
}
if (count($context->results)){
	echo $savvy->render($context->results);
}

if ($like_records) {
    echo $savvy->render($like_records);
}