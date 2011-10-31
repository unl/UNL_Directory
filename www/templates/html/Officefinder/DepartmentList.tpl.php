<?php
if (count($context)) {
    echo '<div class="result_head">'.count($context).' result(s) found</div>';
    echo '<ul class="pfResult departments">';
    foreach ($context as $department) {

        echo $savvy->render($department, 'Officefinder/DepartmentList/ListItem.tpl.php');

    }
    echo '</ul>';
} else {
    echo "No results could be found";
}
?>