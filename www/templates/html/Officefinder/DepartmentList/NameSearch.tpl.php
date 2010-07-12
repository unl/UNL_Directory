<?php
if (count($context)) {
    echo count($context).' results found';
    echo '<ul>';
    foreach ($context as $department) {
        echo '<li><a href="?view=department&amp;id='.$department->id.'">'.$department->name.'</a></li>';
    }
    echo '</ul>';
}

