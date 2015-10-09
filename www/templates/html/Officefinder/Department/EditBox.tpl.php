<div id="editBox">
	<div class="aliases">
		<?php echo $savvy->render($context->getAliases()) ?>
		<?php include __DIR__ . '/../../../editing/Officefinder/Department/Alias/AddForm.tpl.php'; ?>
	</div>
	<div class="users">
		<?php echo $savvy->render($context->getUsers()) ?>
		<?php include __DIR__ . '/../../../editing/Officefinder/Department/User/AddForm.tpl.php'; ?>
	</div>
	<div class="tools">
		<?php include __DIR__ . '/../../../editing/Officefinder/Department/DeleteForm.tpl.php'; ?>
		<?php include __DIR__ . '/../../../editing/Officefinder/Department.tpl.php'; ?>
	</div>
</div>
