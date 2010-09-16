<?php
echo '<h5>Users</h5>';
if (count($context)) {
    echo '<ul class="dept_users">';
    foreach ($context as $user) {
        echo $savvy->render($user);
    }
    echo '</ul>';
}