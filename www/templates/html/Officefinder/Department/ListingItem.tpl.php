<li id="listing-<?php echo $context->id ?>" class="listing" data-listing-id="<?php echo $context->id ?>">
    <?php echo $savvy->render($context, 'Officefinder/Department/Listing.tpl.php') ?>
    <?php $children = $context->getChildren(); ?>
    <?php if (count($children)): ?>
        <?php echo $savvy->render($children, 'Officefinder/Department/Listings.tpl.php') ?>
    <?php endif; ?>
</li>
