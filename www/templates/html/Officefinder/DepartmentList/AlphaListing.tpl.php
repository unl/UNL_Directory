
<?php
foreach ($context as $listing)
{
    echo '<div class="dept">';
    echo '<strong>'.$listing->department->phone.' '.$listing->name.'</strong><br />';
    if ($listing->department->hasChildren()) :
        ?>
        <div id="listings">
        <?php
        $listings = $listing->department->getChildren();
        echo $savvy->render($listings, 'Officefinder/Department/Listings.tpl.php');
        ?>
        </div>
    <?php
    endif;
    echo '</div>';
}
?>
