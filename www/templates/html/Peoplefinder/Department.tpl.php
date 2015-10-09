<?php if (count($context)): ?>
	<?php echo $savvy->render($context, 'Peoplefinder/Department/Personnel.tpl.php') ?>
<?php else: ?>
    No results could be found.
<?php endif; ?>
