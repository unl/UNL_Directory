<div id="editBox" data-department-id="<?php echo $context->id ?>">
	<div class="aliases">
		<?php echo $savvy->render($context->getAliases()) ?>
		<?php echo $savvy->render($context, 'Officefinder/Department/Alias/AddForm.tpl.php') ?>
	</div>
	<div class="users">
		<?php echo $savvy->render($context->getUsers()) ?>
		<?php echo $savvy->render($context, 'Officefinder/Department/User/AddForm.tpl.php') ?>
	</div>
	<div class="forms" data-department-id="<?php echo $context->id ?>">
		<?php echo $savvy->render($context, 'Officefinder/Department/DeleteForm.tpl.php') ?>
		<?php echo $savvy->render($context, 'Officefinder/Department/EditForm.tpl.php') ?>
	</div>
</div>
