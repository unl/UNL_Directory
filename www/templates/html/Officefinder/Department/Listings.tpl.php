<ol class="listings dcf-mb-0 unl-font-sans" role="list">
    <?php foreach ($context as $listing): ?>
        <?php
        if (isset($listing->org_unit)) {
            continue;
        }
        ?>
        <?php echo $savvy->render($listing, 'Officefinder/Department/ListingItem.tpl.php'); ?>
    <?php endforeach; ?>
</ol>
