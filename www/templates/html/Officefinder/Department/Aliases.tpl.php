<?php
echo '<h5><a href="#" class="tooltip" title="Additional keywords to help users find your department, use common acronyms or abbreviations">Aliases</a></h5>';
if (count($context)) {
    echo '<ul class="dept_aliases">';
    foreach ($context as $alias) {
        echo $savvy->render($alias);
    }
    echo '</ul>';
}