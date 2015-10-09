<h2 class="wdn-brand">
	Also known as
	<span class="icon-help" title="Additional names which identify your department. &lsquo;Sometimes people refer to us as&hellip;&rsquo;"></span>
</h2>
<?php if (count($context)): ?>
	<ul class="dept_aliases">
		<?php foreach ($context as $alias): ?>
			<?php echo $savvy->render($alias) ?>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
