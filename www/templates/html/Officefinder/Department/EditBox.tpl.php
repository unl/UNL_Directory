<div id="editBox">
    <div class="aliases">
        <?php
        // Display all aliases
        echo $savvy->render($context->getAliases());
        include dirname(__FILE__).'/../../../editing/Officefinder/Department/AddAliasForm.tpl.php';
        ?>
    </div>
    <div class="users">
        <?php 
        echo $savvy->render($context->getUsers());
        include dirname(__FILE__).'/../../../editing/Officefinder/Department/User/AddForm.tpl.php';
        ?>
    </div>
</div>