<?php
echo '<ul>';
foreach ($context as $var=>$value) {
    echo '<li>'.$var.':'.$value.'</li>';
}
echo '</ul>';

if ($context->userCanEdit(UNL_Officefinder::getUser())) {
    echo '<a href="?view=department&amp;id='.$context->id.'&amp;format=editing">Edit</a><br />';
}

$listings = $context->getListings();
if (count($listings)) {
    echo $savvy->render($listings);
}

if ($department = $context->getHRDepartment()) {
    // This listing has an official HR department associated with IT
    // render all those HR department details.
    echo $savvy->render($department);
}

?>