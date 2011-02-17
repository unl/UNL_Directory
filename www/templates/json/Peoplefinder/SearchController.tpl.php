<?php
UNL_Peoplefinder::$displayResultLimit -= count($context->results);

$like_records = array();
if (!is_array($context->options['q'])
    && !isset($context->options['method'])
    && UNL_Peoplefinder::$displayResultLimit) {
    // More room to display like results
        
    $like_records = $context->options['peoplefinder']->getLikeMatches($context->options['q'], null, $context->results);
}

$all_results = array();

foreach ($context->results as $result) {
    $all_results[] = $savvy->render($result);
}


foreach ($like_records as $result) {
    $all_results[] = $savvy->render($result);
}

echo '['.implode(',', $all_results).']';

?>
