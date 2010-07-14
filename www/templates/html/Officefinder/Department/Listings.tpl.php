<ul class="listings">
<?php
foreach ($context as $listing) {
    echo '<li class="listing" id="listing_'.$listing->id.'">'.$savvy->render($listing).'</li>';
}
?>
</ul>