<?php
echo '<div class="results affiliation '.$context->affiliation.'">';
echo '<h2>'.ucfirst($context->affiliation).'</h2>';
echo $savvy->render(new UNL_Peoplefinder_SearchResults(array('results'=>$context->results)));
if (count($context->like_results)) {
    echo '<div class="likeResults">';
    echo '<h3>similar '.$context->affiliation.' results</h3>';
    echo $savvy->render(new UNL_Peoplefinder_SearchResults(array('results'=>$context->like_results)));
    echo '</div>';
}
echo '</div>';