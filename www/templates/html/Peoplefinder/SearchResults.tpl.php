<?php
$start = 0;
$end   = count($context);
?>
<?php if ($end > $start): ?>
	<div class="result_head"><?php echo $end ?> result<?php echo $end > 1 ? 's' : '' ?> found</div>
	<ul class="pfResult">
		<?php foreach ($context as $record): ?>
			<?php if ($record->getRawObject() instanceof UNL_Peoplefinder_Record): ?>
				<?php echo $savvy->render($record, 'Peoplefinder/RecordInList.tpl.php') ?>
			<?php else: ?>
				<?php echo $savvy->render($record) ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<div class="result_head">No results</div>
<?php endif; ?>
