<ul class="listings sortable">
<?php
foreach ($context as $listing) {
    echo '<li class="listing" id="listing_'.$listing->id.'">'.$savvy->render($listing, 'Officefinder/Department/Listing.tpl.php').'</li>';
}
?>
</ul>