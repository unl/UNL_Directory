<?php
$end = count($context);
?>
<?php if ($end): ?>
	<p class="result_head dcf-txt-xs dcf-mt-1 unl-font-sans"><?php echo $end ?> result<?php echo $end > 1 ? 's' : '' ?> found</p>
	<ul class="pfResult departments dcf-list-bare dcf-m-0 dcf-bg-white dcf-b-1 dcf-b-solid unl-b-light-gray">
		<?php foreach ($context as $department): ?>
			<?php echo $savvy->render($department, 'Officefinder/DepartmentList/ListItem.tpl.php') ?>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p class="result_head dcf-txt-xs dcf-mt-1 unl-font-sans">No results</p>
<?php endif; ?>
