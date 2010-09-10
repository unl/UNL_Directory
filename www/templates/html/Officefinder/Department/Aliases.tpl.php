<?php
if (count($context)) {
    echo '<h5>Aliases</h5>';
    echo '<ul class="dept_aliases">';
    foreach ($context as $alias) {
        echo $savvy->render($alias);
    }
    echo '</ul>';
}