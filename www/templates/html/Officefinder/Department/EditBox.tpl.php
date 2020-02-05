<div id="editBox" data-department-id="<?php echo $context->id ?>">
	<div class="aliases" aria-live="polite">
		<?php echo $savvy->render($context->getAliases()) ?>
		<?php echo $savvy->render($context, 'Officefinder/Department/Alias/AddForm.tpl.php') ?>
	</div>
	<div class="users" aria-live="polite">
		<?php echo $savvy->render($context->getUsers()) ?>
		<?php echo $savvy->render($context, 'Officefinder/Department/User/AddForm.tpl.php') ?>
	</div>
	<div class="forms" data-department-id="<?php echo $context->id ?>">
		<?php echo $savvy->render($context, 'Officefinder/Department/DeleteForm.tpl.php') ?>
		<?php echo $savvy->render($context, 'Officefinder/Department/EditForm.tpl.php') ?>
	</div>
</div>
<aside>
	<h2 class="dcf-txt-h5">My Departments</h2>
	<?php echo $savvy->render(new UNL_Officefinder_User_Departments(), 'Officefinder/User/DepartmentList.tpl.php') ?>
</aside>
