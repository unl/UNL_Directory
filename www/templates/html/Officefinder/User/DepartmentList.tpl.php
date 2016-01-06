<?php if (count($context)): ?>
    <ul>
    	<?php foreach ($context as $department): ?>
        	<li><a href="<?php echo $department->getURL() ?>"><?php echo $department->name ?></a></li>
    	<?php endforeach; ?>
    </ul>
<?php else: ?>
    This user has no departments.
<?php endif; ?>
