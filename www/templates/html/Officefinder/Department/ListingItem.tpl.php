<li id="listing-<?php echo $context->id ?>" class="listing dcf-mb-0 dcf-pt-5 dcf-pr-6 dcf-pb-5 dcf-pl-6" data-listing-id="<?php echo $context->id ?>">
    <?php echo $savvy->render($context, 'Officefinder/Department/Listing.tpl.php') ?>
    <?php $children = $context->getChildren(); ?>
    <?php if (count($children)): ?>
        <?php echo $savvy->render($children, 'Officefinder/Department/Listings.tpl.php') ?>
    <?php endif; ?>
</li>
