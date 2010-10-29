<h2>Printer friendly version for <?php echo UNL_Officefinder::getUser(true); ?>, on <?php echo date('F jS, Y'); ?></h2>
<?php
foreach ($context as $listing)
{
    echo '<div class="dept">';
    echo '<strong>'.$listing->department->phone.' '.$listing->name.'</strong><br />';
    $listings = $listing->department->getChildren();
    if (count($listings)) :
        ?>
        <div id="listings">
        <?php
        echo $savvy->render($listings, 'Officefinder/Department/Listings.tpl.php');
        ?>
        </div>
    <?php
    endif;
    echo '</div>';
}
?>
