<?php
foreach ($context as $listing)
{
    echo '<div class="dept">';
    echo '<strong>'.$listing->name.'</strong>';
    echo '<strong>'.$savvy->render($listing->department->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</strong><br />';
    $listings = $listing->department->getChildren();
    if (count($listings)) :
        ?>
        <div class="dcf-mb-7" id="listings">
        <?php
        echo $savvy->render($listings, 'Officefinder/Department/Listings.tpl.php');
        ?>
        </div>
    <?php
    endif;
    echo '</div>';
}
?>
