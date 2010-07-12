<?php
echo '<ul>';
foreach ($context as $var=>$value) {
    echo '<li>'.$var.':'.$value.'</li>';
}
echo '</ul>';

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