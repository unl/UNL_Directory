<li>
	<a href="<?php echo UNL_Peoplefinder::getURL() . 'people/' . $context->uid; ?>"><?php echo $context->uid; ?></a>
	<?php echo $savvy->render($context, 'Officefinder/Department/User/DeleteForm.tpl.php') ?>
</li>
