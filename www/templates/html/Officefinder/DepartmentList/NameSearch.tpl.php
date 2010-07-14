<?php
if (count($context)) {
    echo '<div class="result_head">'.count($context).' result(s) found</div>';
    echo '<ul>';
    foreach ($context as $department) {
        echo '<li><a href="?view=department&amp;id='.$department->id.'">'.$department->name.'</a></li>';
    }
    echo '</ul>';
}

