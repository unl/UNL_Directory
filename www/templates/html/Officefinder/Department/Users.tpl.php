<?php
if (count($context)) {
    echo '<h5>Users</h5>';
    echo '<ul class="dept_users">';
    foreach ($context as $user) {
        echo $savvy->render($user);
    }
    echo '</ul>';
}