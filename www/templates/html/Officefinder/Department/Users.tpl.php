<?php
echo '<h5><a href="#" class="tooltip" title="Give others permission to edit this entry, and ALL children">Users</a></h5>';
if (count($context)) {
    echo '<ul class="dept_users">';
    foreach ($context as $user) {
        echo $savvy->render($user);
    }
    echo '</ul>';
}