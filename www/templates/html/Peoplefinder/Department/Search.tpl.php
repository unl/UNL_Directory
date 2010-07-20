<?php
if (count($context)) {
    echo '<h2>Search results for '.htmlentities($context->options['q']).'</h2><ul class="departments">';
    foreach($context as $department) {
        echo '<li class="ppl_Sresult"><a href="'.UNL_Peoplefinder::getURL().'departments/?d='.urlencode($department->name).'">'.$department->name.'</a></li>';
    }
    echo '</ul>';
} else {
    echo 'No results could be found.';
}