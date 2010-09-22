<h2>My Departments</h2>
<?php
if (count($context)) {
    echo '<ul>';
    foreach ($context as $department) {
        echo '<li><a href="'.$department->getURL().'">'.$department->name.'</li>';
    }
    echo '</ul>';
} else {
    echo 'This user has no departments.';
}