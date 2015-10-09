<?php
$class = 'listings';
if ($parent->context instanceof UNL_Officefinder_Department && $parent->context->userCanEdit(UNL_Officefinder::getUser())) {
    $class .= ' sortable';
}
?>
<ul class="<?php echo $class; ?>">
    <?php foreach ($context as $listing): ?>
        <?php
        if (isset($listing->org_unit)) {
            continue;
        }
        ?>
        <li class="listing" id="listing_<?php echo $listing->id ?>">
            <?php echo $savvy->render($listing, 'Officefinder/Department/Listing.tpl.php') ?>
            <?php
            $children = $listing->getChildren();
            ?>
            <?php if (count($children)): ?>
                <?php echo $savvy->render($children, 'Officefinder/Department/Listings.tpl.php') ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
