<h2 class="wdn-brand">
    Editors
    <span class="icon-help" title="Give others permission to edit this entry and ALL children."></span>
</h2>
<?php if (count($context)): ?>
    <ul class="dept_users">
        <?php foreach ($context as $user): ?>
            <?php echo $savvy->render($user) ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php echo $savvy->render(null, 'Officefinder/Department/User/Template.tpl.php') ?>
