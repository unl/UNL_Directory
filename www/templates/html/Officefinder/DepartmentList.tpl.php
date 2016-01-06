<?php
$end = count($context);
?>
<?php if ($end): ?>
	<div class="result_head"><?php echo $end ?> result<?php echo $end > 1 ? 's' : '' ?> found</div>
	<ul class="pfResult departments">
		<?php foreach ($context as $department): ?>
			<?php echo $savvy->render($department, 'Officefinder/DepartmentList/ListItem.tpl.php') ?>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<div class="result_head">No results</div>
<?php endif; ?>
