<?php
echo '<h5>Aliases</h5>';
if (count($context)) {
    echo '<ul class="dept_aliases">';
    foreach ($context as $alias) {
        echo $savvy->render($alias);
    }
    echo '</ul>';
}