<h2 class="wdn-brand">
	Also known as
	<span class="icon-help" title="Additional names which identify your department. &lsquo;Sometimes people refer to us as&hellip;&rsquo;"></span>
</h2>
<?php if (count($context)): ?>
	<ul class="dept_aliases" aria-live="polite">
		<?php foreach ($context as $alias): ?>
			<?php echo $savvy->render($alias) ?>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php echo $savvy->render(null, 'Officefinder/Department/Alias/Template.tpl.php') ?>
