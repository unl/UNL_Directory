<?php
$end = count($context);
?>
<?php if ($end): ?>
	<p class="result_head dcf-txt-xs dcf-mt-1 unl-font-sans unl-dark-gray"><?php echo $end ?> result<?php echo $end > 1 ? 's' : '' ?> found</p>
	<ul class="pfResult departments" role="list">
		<?php foreach ($context as $department): ?>
			<?php echo $savvy->render($department, 'Officefinder/DepartmentList/ListItem.tpl.php') ?>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p class="result_head dcf-txt-xs dcf-mt-1 unl-font-sans unl-dark-gray">No results</p>
<?php endif; ?>
