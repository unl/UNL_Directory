<h2>
    Also known as
    <svg class="dcf-ml-1 dcf-h-4 dcf-w-4 dcf-fill-current" role="img" height="24" width="24" viewBox="0 0 24 24">
        <title>Additional names which identify your department. &lsquo;Sometimes people refer to us as&hellip;&rsquo;</title>
        <path d="M11.5 1C5.159 1 0 6.159 0 12.5S5.159 24 11.5 24 23 18.841 23 12.5 17.841 1 11.5 1zm0 22C5.71 23 1 18.29 1 12.5S5.71 2 11.5 2 22 6.71 22 12.5 17.29 23 11.5 23z"></path>
        <path d="M11.5 6.5C9.57 6.5 8 8.07 8 10a.5.5 0 001 0c0-1.378 1.121-2.5 2.5-2.5S14 8.622 14 10s-1.121 2.5-2.5 2.5a.5.5 0 00-.5.5v3a.5.5 0 001 0v-2.535A3.506 3.506 0 0015 10c0-1.93-1.57-3.5-3.5-3.5z"></path>
        <circle cx="11.5" cy="18.5" r="1"></circle>
    </svg>
</h2>
<?php if (count($context)): ?>
    <ul class="dept_aliases">
        <?php foreach ($context as $alias): ?>
            <?php echo $savvy->render($alias) ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php echo $savvy->render(null, 'Officefinder/Department/Alias/Template.tpl.php') ?>
