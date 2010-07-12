<?php
if (count($context)) {
    echo count($context).' results found';
    echo '<ul>';
    foreach ($context as $department) {
        echo '<li>'.$department->name.'</li>';
    }
    echo '</ul>';
}

