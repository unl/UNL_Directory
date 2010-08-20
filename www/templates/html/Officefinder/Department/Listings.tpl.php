<ul class="listings sortable">
<?php
foreach ($context as $listing) {
    if (isset($listing->org_unit)) {
        continue;
    }
    echo '<li class="listing" id="listing_'.$listing->id.'">'.$savvy->render($listing, 'Officefinder/Department/Listing.tpl.php');
    if ($listing->hasChildren()) {
        echo $savvy->render($listing->getChildren(), 'Officefinder/Department/Listings.tpl.php');
    }
    echo '</li>';
}
?>
</ul>