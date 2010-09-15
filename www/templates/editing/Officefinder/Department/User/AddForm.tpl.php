<form action="<?php echo $context->getURL(); ?>" method="post" id="adduser_<?php echo $context->id; ?>" class="add">
    <input type="hidden" name="_type" value="add_dept_user" />
    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
    <input type="text" name="uid" />
    <input type="submit" value="Add user" />
</form>