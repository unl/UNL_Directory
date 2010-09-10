<form action="<?php echo $context->getURL(); ?>" method="post" id="addalias_<?php echo $context->id; ?>" style="width:120px;">
    <input type="hidden" name="_type" value="add_dept_alias" />
    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
    <input type="text" name="name" />
    <input type="submit" value="Add alias" />
</form>