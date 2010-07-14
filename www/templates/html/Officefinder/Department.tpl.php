<?php
echo '<ul>';
foreach ($context as $var=>$value) {
    echo '<li>'.$var.':'.$value.'</li>';
}
echo '</ul>';

if ($context->userCanEdit(UNL_Officefinder::getUser())) {
    echo '<a href="?view=department&amp;id='.$context->id.'&amp;format=editing">Edit</a><br />';
}
?>
<ul class="wdn_tabs">
    <li><a href="#listings">Listings</a></li>
    <li><a href="#employees">Employees</a></li>
</ul>
<div class="wdn_tabs_content">
    <div id="listings">
    <?php
    $listings = $context->getListings();
    if (count($listings)) {
        echo $savvy->render($listings);
    }
    ?>
    </div>
    <div id="employees">
    <?php
    if ($department = $context->getHRDepartment()) {
        // This listing has an official HR department associated with IT
        // render all those HR department details.
        echo $savvy->render($department);
    }
    
    ?>
    </div>
</div>