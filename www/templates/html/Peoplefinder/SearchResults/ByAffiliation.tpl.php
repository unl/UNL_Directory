<?php
echo '<div id="results_'.$context->affiliation.'" class="results affiliation '.$context->affiliation.'">';
echo '<h3>'.ucfirst($context->affiliation).'</h3>';
echo $savvy->render(new UNL_Peoplefinder_SearchResults(array('results'=>$context->getRaw('results'), 'options'=>$context->options )));
if (count($context->like_results)) {
    echo '<div class="likeResults">';
    echo '<h3>similar '.$context->affiliation.' results</h3>';
    echo $savvy->render(new UNL_Peoplefinder_SearchResults(array('results'=>$context->getRaw('like_results'), 'options'=>$context->options)));
    echo '</div>';
}
echo '</div>';